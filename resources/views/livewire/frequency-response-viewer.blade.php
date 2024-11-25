<div>
    @push('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"></script>
    @endpush
        <div class="card"
             x-data="{
        amplitudeData: @js($chartData),
        summedResponse: @js($summedResponse),
        phaseData: @js($phaseData),
        amplitudeChart: null,
        phaseChart: null
     }"
             x-init="() => {
        $nextTick(() => {
            // Amplitude Chart
            const ctxAmplitude = document.getElementById('frequencyResponseChart').getContext('2d');
            let datasetsAmplitude = amplitudeData;

            if (datasetsAmplitude.length > 0 && summedResponse) {
                datasetsAmplitude.push({
                    label: 'Summed Response',
                    data: summedResponse,
                    borderColor: '#000000',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false
                });
            }

            amplitudeChart = new Chart(ctxAmplitude, {
                type: 'line',
                data: {
                    datasets: datasetsAmplitude
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: {
                            type: 'logarithmic',
                            title: {
                                display: true,
                                text: 'Frequency (Hz)'
                            },
                            min: 20,
                            max: 20000,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Amplitude (dB)'
                            },
                            min: 55,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + ' dB';
                                }
                            }
                        }
                    },
                    parsing: {
                        xAxisKey: 'x',
                        yAxisKey: 'y'
                    },
                    elements: {
                        point: {
                            radius: 0, // Hide the points
                            hoverRadius: 5 // Show points on hover
                        },
                        line: {
                            tension: 0.3 // Add slight smoothing to the line
                        }
                    }
                }
            });

            // Phase Chart
            const ctxPhase = document.getElementById('phaseResponseChart').getContext('2d');
            phaseChart = new Chart(ctxPhase, {
                type: 'line',
                data: {
                    datasets: phaseData
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: {
                            type: 'logarithmic',
                            title: {
                                display: true,
                                text: 'Frequency (Hz)'
                            },
                            min: 20,
                            max: 20000,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Phase (degrees)'
                            },
                            min: -180,
                            max: 180,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '°';
                                }
                            }
                        }
                    },
                    parsing: {
                        xAxisKey: 'x',
                        yAxisKey: 'y'
                    },
                    elements: {
                        point: {
                            radius: 0, // Hide the points
                            hoverRadius: 5 // Show points on hover
                        },
                        line: {
                            tension: 0.3 // Add slight smoothing to the line
                        }
                    }
                }
            });

            // Cleanup on disconnect
            $cleanup(() => {
                if (amplitudeChart) amplitudeChart.destroy();
                if (phaseChart) phaseChart.destroy();
            });
        });
     }">

            <div class="card-body">
                <div class="relative h-[400px] mb-8">
                    <h4 class="text-lg font-semibold mb-2">Amplitude Response</h4>
                    <canvas id="frequencyResponseChart"></canvas>
                </div>

                <div class="relative h-[400px]">
                    <h4 class="text-lg font-semibold mb-2">Phase Response</h4>
                    <canvas id="phaseResponseChart"></canvas>
                </div>
            </div>

{{--            @if(config('app.debug'))--}}
{{--                <div class="mt-4 p-4 bg-gray-100 rounded">--}}
{{--                    <h4 class="text-lg font-semibold mb-2">Debug Information:</h4>--}}
{{--                    <pre class="text-sm" x-text="JSON.stringify({--}}
{{--                datasetCount: amplitudeData?.length,--}}
{{--                phaseCount: phaseData?.length--}}
{{--            }, null, 2)"></pre>--}}
{{--                </div>--}}
{{--            @endif--}}
        </div>
