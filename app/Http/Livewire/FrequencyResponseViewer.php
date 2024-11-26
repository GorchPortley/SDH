<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FrequencyResponseViewer extends Component
{
    public $design;
    public $chartData = [];
    public $summedResponse = [];
    public $frequencies = [];
    public $debugInfo = [];
    public $phaseData = [];

    public function mount($design)
    {
        $this->design = $design;
        $this->debugInfo['has_design'] = !is_null($design);
        $this->debugInfo['frd_files_type'] = gettype($design->frd_files);
        $this->loadFrequencyResponses();
    }

    protected function loadFrequencyResponses()
    {
        if (empty($this->design->frd_files)) {
            $this->debugInfo['error'] = 'No FRD files found in design';
            return;
        }

        Log::debug('Starting frequency response calculation', [
            'design_id' => $this->design->id,
            'file_count' => count($this->design->frd_files)
        ]);

        $frdFiles = is_string($this->design->frd_files)
            ? json_decode($this->design->frd_files, true)
            : $this->design->frd_files;

        $this->chartData = [];
        $this->phaseData = [];
        $summedData = [];
        $firstResponse = true;

        foreach ($frdFiles as $index => $filePath) {
            if (!Storage::exists($filePath)) {
                $this->debugInfo['missing_files'][] = $filePath;
                continue;
            }

            $content = Storage::get($filePath);
            $data = $this->parseFRDFile($content);

            if (!empty($data)) {
                Log::debug('Parsed FRD file', [
                    'file' => $filePath,
                    'points' => count($data),
                    'freq_range' => [
                        'min' => min(array_column($data, 'frequency')),
                        'max' => max(array_column($data, 'frequency'))
                    ],
                    'amp_range' => [
                        'min' => min(array_column($data, 'amplitude')),
                        'max' => max(array_column($data, 'amplitude'))
                    ]
                ]);

                $label = pathinfo($filePath, PATHINFO_FILENAME);

                // Format data for chart.js
                $this->chartData[] = [
                    'label' => $label,
                    'data' => array_map(function ($point) {
                        return [
                            'x' => $point['frequency'],
                            'y' => $point['amplitude']
                        ];
                    }, $data),
                    'borderColor' => $this->getRandomColor(),
                    'fill' => false
                ];

                $this->phaseData[] = [
                    'label' => $label . ' Phase',
                    'data' => array_map(function ($point) {
                        return [
                            'x' => $point['frequency'],
                            'y' => $point['phase']
                        ];
                    }, $data),
                    'borderColor' => $this->getRandomColor(),
                    'fill' => false
                ];

                foreach ($data as $point) {
                    $freq = $point['frequency'];

                    // More careful handling of low frequency amplitudes
                    $amplitude = pow(10, $point['amplitude'] / 20.0);

                    // Additional low frequency amplitude compensation
                    if ($freq < 100) {
                        // Slightly reduce the impact of low frequencies in the sum
                        $amplitude *= (0.7 + (0.3 * ($freq / 100)));
                    }

                    // Normalize phase to -180 to +180 range
                    $phase = $point['phase'];
                    while ($phase > 180) $phase -= 360;
                    while ($phase < -180) $phase += 360;

                    // Additional phase smoothing for low frequencies
                    if ($freq < 100) {
                        $phase *= (0.8 + (0.2 * ($freq / 100)));
                    }

                    $phaseRad = deg2rad($phase);

                    $real = $amplitude * cos($phaseRad);
                    $imag = $amplitude * sin($phaseRad);

                    if (!isset($summedData[$freq])) {
                        $summedData[$freq] = [
                            'real' => $real,
                            'imag' => $imag,
                            'count' => 1
                        ];
                    } else {
                        $summedData[$freq]['real'] += $real;
                        $summedData[$freq]['imag'] += $imag;
                        $summedData[$freq]['count']++;
                    }
                }
            }
        }

        // Convert summed complex response back to amplitude and phase
        if (!empty($summedData)) {
            ksort($summedData);

            $this->summedResponse = [];
            $lastValidDb = null;

            foreach ($summedData as $freq => $complex) {
                $magnitude = sqrt(pow($complex['real'], 2) + pow($complex['imag'], 2));
                $db = 20 * log10(max($magnitude, 1e-20));

                // Additional smoothing for low frequencies
                if ($freq < 100 && $lastValidDb !== null) {
                    $dbDiff = abs($db - $lastValidDb);
                    if ($dbDiff > 3) {
                        $db = $lastValidDb + (3 * ($db > $lastValidDb ? 1 : -1));
                    }
                }

                $db = max(min($db, 120), -120);
                $lastValidDb = $db;

                $this->summedResponse[] = [
                    'x' => $freq,
                    'y' => $db
                ];
            }
        }
    }

        protected
        function parseFRDFile($content)
        {
            $lines = explode("\n", trim($content));
            $data = [];
            $lastValidAmplitude = null;
            $lastValidFreq = null;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line[0] === '*' || $line[0] === '#') {
                    continue;
                }

                $values = preg_split('/\s+/', $line);
                if (count($values) >= 3) {
                    $freq = (float)$values[0];
                    $amp = (float)$values[1];
                    $phase = (float)$values[2];

                    // Validate frequency range
                    if ($freq >= 20 && $freq <= 20000) {
                        // Check for unrealistic amplitude jumps at low frequencies
                        if ($freq < 100 && $lastValidAmplitude !== null) {
                            $ampDiff = abs($amp - $lastValidAmplitude);
                            $freqRatio = $freq / $lastValidFreq;

                            // If there's a sudden amplitude jump (>3dB) in a small frequency range
                            if ($ampDiff > 3 && $freqRatio > 0.8) {
                                // Use the last valid amplitude instead
                                $amp = $lastValidAmplitude;
                            }
                        }

                        if ($amp >= -120 && $amp <= 120) {
                            $lastValidAmplitude = $amp;
                            $lastValidFreq = $freq;

                            $data[] = [
                                'frequency' => $freq,
                                'amplitude' => $amp,
                                'phase' => $phase
                            ];
                        }
                    }
                }
            }

            // Sort by frequency and apply smoothing to low frequencies
            usort($data, fn($a, $b) => $a['frequency'] <=> $b['frequency']);

            // Apply moving average smoothing to frequencies below 100Hz
            $smoothedData = [];
            $windowSize = 3; // Adjust this value to change smoothing amount

            foreach ($data as $i => $point) {
                if ($point['frequency'] < 100) {
                    $sum = 0;
                    $count = 0;

                    // Calculate moving average
                    for ($j = max(0, $i - $windowSize); $j <= min(count($data) - 1, $i + $windowSize); $j++) {
                        if ($data[$j]['frequency'] < 100) {
                            $sum += $data[$j]['amplitude'];
                            $count++;
                        }
                    }

                    $smoothedAmp = $count > 0 ? $sum / $count : $point['amplitude'];
                    $smoothedData[] = [
                        'frequency' => $point['frequency'],
                        'amplitude' => $smoothedAmp,
                        'phase' => $point['phase']
                    ];
                } else {
                    $smoothedData[] = $point;
                }
            }

            return $smoothedData;
        }

        protected
        function complexAdd($a, $b)
        {
            return [
                'real' => $a['real'] + $b['real'],
                'imag' => $a['imag'] + $b['imag']
            ];
        }

        protected
        function complexMagnitude($complex)
        {
            return sqrt(pow($complex['real'], 2) + pow($complex['imag'], 2));
        }

        protected
        function complexPhase($complex)
        {
            return rad2deg(atan2($complex['imag'], $complex['real']));
        }

        protected
        function getRandomColor()
        {
            $colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ];

            static $colorIndex = 0;
            return $colors[$colorIndex++ % count($colors)];
        }

        public
        function render()
        {
            return view('livewire.frequency-response-viewer', [
                'chartData' => $this->chartData,
                'frequencies' => $this->frequencies,
                'summedResponse' => $this->summedResponse,
                'phaseData' => $this->phaseData,
                'debugInfo' => $this->debugInfo
            ]);
        }
    }
