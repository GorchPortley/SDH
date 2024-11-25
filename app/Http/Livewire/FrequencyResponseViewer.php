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
                $label = pathinfo($filePath, PATHINFO_FILENAME);

                // Format data for chart.js
                $this->chartData[] = [
                    'label' => $label,
                    'data' => array_map(function($point) {
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
                    'data' => array_map(function($point) {
                        return [
                            'x' => $point['frequency'],
                            'y' => $point['phase']
                        ];
                    }, $data),
                    'borderColor' => $this->getRandomColor(),
                    'fill' => false
                ];

                // Create frequency-indexed array for complex summation
                foreach ($data as $point) {
                    $freq = $point['frequency'];
                    // Convert amplitude to linear pressure and phase to radians
                    $amplitude = pow(10, $point['amplitude'] / 20);
                    $phaseRad = deg2rad($point['phase']);

                    // Convert to complex components
                    $real = $amplitude * cos($phaseRad);
                    $imag = $amplitude * sin($phaseRad);

                    if (!isset($summedData[$freq])) {
                        $summedData[$freq] = ['real' => $real, 'imag' => $imag];
                    } else {
                        // Add complex components
                        $summedData[$freq]['real'] += $real;
                        $summedData[$freq]['imag'] += $imag;
                    }
                }
            }
        }

        // Convert summed complex response back to amplitude and phase
        if (!empty($summedData)) {
            // Sort by frequency
            ksort($summedData);

            $this->summedResponse = array_map(function($freq, $complex) {
                // Convert complex sum back to amplitude
                $magnitude = sqrt(
                    pow($complex['real'], 2) +
                    pow($complex['imag'], 2)
                );

                return [
                    'x' => $freq,
                    'y' => 20 * log10(max($magnitude, 1e-10))
                ];
            }, array_keys($summedData), array_values($summedData));
        }

        // Add debug info
        $this->debugInfo['chart_data_count'] = count($this->chartData);
        foreach ($this->chartData as $index => $dataset) {
            $this->debugInfo['response_' . $index . '_points'] = count($dataset['data']);
            $xValues = array_column($dataset['data'], 'x');
            $yValues = array_column($dataset['data'], 'y');
            $this->debugInfo['response_' . $index . '_range'] = [
                'freq_min' => min($xValues),
                'freq_max' => max($xValues),
                'amp_min' => min($yValues),
                'amp_max' => max($yValues)
            ];
        }

        if (!empty($this->summedResponse)) {
            $this->debugInfo['summed_points'] = count($this->summedResponse);
            $summedX = array_column($this->summedResponse, 'x');
            $summedY = array_column($this->summedResponse, 'y');
            $this->debugInfo['summed_range'] = [
                'freq_min' => min($summedX),
                'freq_max' => max($summedX),
                'amp_min' => min($summedY),
                'amp_max' => max($summedY)
            ];
        }
    }

    protected function parseFRDFile($content)
    {
        $lines = explode("\n", trim($content));
        $data = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '*' || $line[0] === '#') {
                continue;
            }

            $values = preg_split('/\s+/', $line);
            if (count($values) >= 3) {
                $data[] = [
                    'frequency' => (float) $values[0],
                    'amplitude' => (float) $values[1],
                    'phase' => (float) $values[2]
                ];
            }
        }

        return $data;
    }

    protected function dbToLinear($db)
    {
        return pow(10, $db / 20);
    }

    protected function linearToDb($linear)
    {
        return 20 * log10(max($linear, 1e-10)); // Prevent log of 0
    }

    protected function getRandomColor()
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
        ];

        static $colorIndex = 0;
        return $colors[$colorIndex++ % count($colors)];
    }

    public function render()
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
