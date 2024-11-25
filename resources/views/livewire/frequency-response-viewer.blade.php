<div class="rounded-lg">
    @push('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"></script>
    @endpush

    <div class="w-full"
         x-data="{
            activeTab: 'future',
            amplitudeData: @js($chartData),
            summedResponse: @js($summedResponse),
            phaseData: @js($phaseData),
            amplitudeChart: null,
            phaseChart: null,

            initCharts() {
    if (this.activeTab === 'amplitude') {
        if (this.amplitudeChart) {
            this.amplitudeChart.destroy();
        }

        this.$nextTick(() => {
            const ctxAmplitude = document.getElementById('frequencyResponseChart').getContext('2d');
            // Create a deep copy of the amplitude data
            let datasetsAmplitude = JSON.parse(JSON.stringify(this.amplitudeData));

            if (datasetsAmplitude.length > 0 && this.summedResponse) {
                datasetsAmplitude.push({
                    label: 'Summed Response',
                    data: this.summedResponse,
                    borderColor: '#000000',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false
                });
            }

                        this.amplitudeChart = new Chart(ctxAmplitude, {
                            type: 'line',
                            data: { datasets: datasetsAmplitude },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    x: {
                                        type: 'logarithmic',
                                        title: { display: true, text: 'Frequency (Hz)' },
                                        min: 20,
                                        max: 20000,
                                        grid: { color: 'rgba(0, 0, 0, 0.1)' }
                                    },
                                    y: {
                                        title: { display: true, text: 'Amplitude (dB)' },
                                        min: 55,
                                        max: 100,
                                        grid: { color: 'rgba(0, 0, 0, 0.1)' }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { usePointStyle: true, padding: 20 }
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
                                parsing: { xAxisKey: 'x', yAxisKey: 'y' },
                                elements: {
                                    point: { radius: 0, hoverRadius: 5 },
                                    line: { tension: 0.3 }
                                }
                            }
                        });
                    });
                }

                if (this.activeTab === 'phase') {
                    if (this.phaseChart) {
                        this.phaseChart.destroy();
                    }

                    this.$nextTick(() => {
                        const ctxPhase = document.getElementById('phaseResponseChart').getContext('2d');
                        this.phaseChart = new Chart(ctxPhase, {
                            type: 'line',
                            data: { datasets: this.phaseData },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                scales: {
                                    x: {
                                        type: 'logarithmic',
                                        title: { display: true, text: 'Frequency (Hz)' },
                                        min: 20,
                                        max: 20000,
                                        grid: { color: 'rgba(0, 0, 0, 0.1)' }
                                    },
                                    y: {
                                        title: { display: true, text: 'Phase (degrees)' },
                                        min: -180,
                                        max: 180,
                                        grid: { color: 'rgba(0, 0, 0, 0.1)' }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: { usePointStyle: true, padding: 20 }
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
                                parsing: { xAxisKey: 'x', yAxisKey: 'y' },
                                elements: {
                                    point: { radius: 0, hoverRadius: 5 },
                                    line: { tension: 0.3 }
                                }
                            }
                        });
                    });
                }
            }
        }"
         x-init="initCharts()"
         @tab-changed.window="activeTab = $event.detail.tab; initCharts();">

        <!-- Mobile Tabs (top) -->
        <div class="lg:hidden w-full rounded-t-lg bg-zinc-800 p-4">
            <div class="flex space-x-2 overflow-x-auto">
                <x-button
                    @click="activeTab = 'amplitude'; initCharts()"
                    :class="{ 'bg-zinc-600': activeTab === 'amplitude' }"
                    class="flex-shrink-0 px-4 py-2 rounded text-white hover:bg-zinc-700 transition-colors"
                >
                    Amplitude
                </x-button>
                <x-button
                    @click="activeTab = 'phase'; initCharts()"
                    :class="{ 'bg-zinc-600': activeTab === 'phase' }"
                    class="flex-shrink-0 px-4 py-2 rounded text-white hover:bg-zinc-700 transition-colors"
                >
                    Phase
                </x-button>
                <x-button
                    @click="activeTab = 'future'"
                    :class="{ 'bg-zinc-600': activeTab === 'future' }"
                    class="flex-shrink-0 px-4 py-2 rounded text-white hover:bg-zinc-700 transition-colors"
                >
                    Future
                </x-button>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="flex rounded-lg border border-gray-400 flex-col lg:flex-row">
            <!-- Desktop Sidebar Tabs (left) -->
            <div class="hidden border-r border-gray-400 lg:block lg:w-1/4 p-4">
                <div class="space-y-2 flex flex-col items-center">
                    <x-button
                        @click="activeTab = 'future'"
                        :class="{ 'bg-zinc-600': activeTab === 'future' }"
                        class="w-full"
                    >
                        Components
                    </x-button>
                    <x-button
                        @click="activeTab = 'amplitude'; initCharts()"
                        :class="{ 'bg-zinc-600': activeTab === 'amplitude' }"
                        class="w-full"
                    >
                        Amplitude Response
                    </x-button>
                    <x-button
                        @click="activeTab = 'phase'; initCharts()"
                        :class="{ 'bg-zinc-600': activeTab === 'phase' }"
                        class="w-full"
                    >
                        Phase Response
                    </x-button>

                </div>
            </div>

                <!-- Main Content -->
                <div class="w-full rounded-lg p-4 lg:p-6">
                    <!-- Content Container -->
                    <div class="w-full h-full bg-white rounded-lg shadow-sm">
                        <!-- Tab Content -->
                        <div class="h-full">

                            <!-- Future Content Tab -->
                            <div x-show="activeTab === 'future'" class="h-full">
                                <!-- Components -->
                                @if($design->components->count() > 0)
                                    <div class="">
                                        <h2 class="text-xl font-semibold text-gray-900 border-b-2 p-2 border-zinc-400">Components</h2>
                                        <table class="mt-4 divide-y divide-gray-200">
                                            @foreach($design->components as $component)
                                                <div class="border-b py-2">
                                                    <div class="flex justify-between">
                                                        <div class="flex-1">
                                                            <h4 class="text-lg font-medium text-gray-900">
                                                                {{$component->driver->brand}} - {{$component->driver->model}}
                                                            </h4>
                                                            <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                                                <span>{{$component->position}}</span>
                                                                <span>•</span>
                                                                <span>{{$component->driver->size}}</span>
                                                                <span>•</span>
                                                                <span>{{$component->driver->category}}</span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4 flex flex-col items-end">
                                                            <span class="text-sm font-medium text-gray-900">Qty: {{$component->quantity}}</span>
                                                            <span class="mt-1 text-sm text-gray-500">
                                {{$component->low_frequency}} Hz - {{$component->high_frequency}} Hz
                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- Amplitude Response Tab -->
                            <div x-show="activeTab === 'amplitude'" class="h-full p-4">
                                <h4 class="text-xl font-semibold mb-4">Amplitude Response</h4>
                                <div class=" lg:h-[calc(100%-2rem)]">
                                    <canvas id="frequencyResponseChart"></canvas>
                                </div>
                            </div>

                            <!-- Phase Response Tab -->
                            <div x-show="activeTab === 'phase'" class="h-full p-4">
                                <h4 class="text-xl font-semibold mb-4">Phase Response</h4>
                                <div class=" lg:h-[calc(100%-2rem)]">
                                    <canvas id="phaseResponseChart"></canvas>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
