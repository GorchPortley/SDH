<div>
    @push('head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js"></script>
    @endpush

    <div class="w-full max-w-[1920px] mx-auto"
         x-data="{
            activeTab: 'amplitude',
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
                        let datasetsAmplitude = this.amplitudeData;

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
                                maintainAspectRatio: false,
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
                                maintainAspectRatio: false,
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
        <div class="lg:hidden w-full bg-zinc-800 p-4">
            <div class="flex space-x-2 overflow-x-auto">
                <button
                    @click="activeTab = 'amplitude'; initCharts()"
                    :class="{ 'bg-zinc-600': activeTab === 'amplitude' }"
                    class="flex-shrink-0 px-4 py-2 rounded text-white hover:bg-zinc-700 transition-colors"
                >
                    Amplitude
                </button>
                <button
                    @click="activeTab = 'phase'; initCharts()"
                    :class="{ 'bg-zinc-600': activeTab === 'phase' }"
                    class="flex-shrink-0 px-4 py-2 rounded text-white hover:bg-zinc-700 transition-colors"
                >
                    Phase
                </button>
                <button
                    @click="activeTab = 'future'"
                    :class="{ 'bg-zinc-600': activeTab === 'future' }"
                    class="flex-shrink-0 px-4 py-2 rounded text-white hover:bg-zinc-700 transition-colors"
                >
                    Future
                </button>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="flex flex-col lg:flex-row">
            <!-- Desktop Sidebar Tabs (left) -->
            <div class="hidden lg:block lg:w-1/4 bg-zinc-800 p-4">
                <div class="space-y-2">
                    <button
                        @click="activeTab = 'amplitude'; initCharts()"
                        :class="{ 'bg-zinc-600': activeTab === 'amplitude' }"
                        class="w-full text-left px-4 py-3 rounded text-white hover:bg-zinc-700 transition-colors"
                    >
                        Amplitude Response
                    </button>
                    <button
                        @click="activeTab = 'phase'; initCharts()"
                        :class="{ 'bg-zinc-600': activeTab === 'phase' }"
                        class="w-full text-left px-4 py-3 rounded text-white hover:bg-zinc-700 transition-colors"
                    >
                        Phase Response
                    </button>
                    <button
                        @click="activeTab = 'future'"
                        :class="{ 'bg-zinc-600': activeTab === 'future' }"
                        class="w-full text-left px-4 py-3 rounded text-white hover:bg-zinc-700 transition-colors"
                    >
                        Future Content
                    </button>
                </div>
            </div>

                <!-- Main Content -->
                <div class="lg:w-3/4 p-4 lg:p-6">
                    <!-- Content Container -->
                    <div class="w-full h-full bg-white rounded-lg shadow-sm">
                        <!-- Tab Content -->
                        <div class="h-full">
                            <!-- Amplitude Response Tab -->
                            <div x-show="activeTab === 'amplitude'" class="h-full p-4">
                                <h4 class="text-xl font-semibold mb-4">Amplitude Response</h4>
                                <div class="h-[500px] lg:h-[calc(100%-2rem)]">
                                    <canvas id="frequencyResponseChart"></canvas>
                                </div>
                            </div>

                            <!-- Phase Response Tab -->
                            <div x-show="activeTab === 'phase'" class="h-full p-4">
                                <h4 class="text-xl font-semibold mb-4">Phase Response</h4>
                                <div class="h-[500px] lg:h-[calc(100%-2rem)]">
                                    <canvas id="phaseResponseChart"></canvas>
                                </div>
                            </div>

                            <!-- Future Content Tab -->
                            <div x-show="activeTab === 'future'" class="h-full p-4">
                                <h4 class="text-xl font-semibold mb-4">Future Content</h4>
                                <div class="flex items-center justify-center h-[500px] lg:h-[calc(100%-2rem)] bg-zinc-100 rounded-lg">
                                    <p class="text-zinc-500">Additional content coming soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
