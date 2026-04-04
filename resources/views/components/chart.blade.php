@props([
    'type' => 'line',
    'labels' => [],
    'datasets' => [],
    'options' => [],
])

<div x-data="{
    chart: null,
    labels: @js($labels),
    datasets: @js($datasets),
    options: @js($options),
    init() {
        const isMobile = window.innerWidth < 768;
        
        // Deep merge responsive legend
        if (this.options.plugins && this.options.plugins.legend) {
            this.options.plugins.legend.position = isMobile ? 'bottom' : (this.options.plugins.legend.position || 'top');
        }

        this.chart = new Chart(this.$refs.canvas, {
            type: '{{ $type }}',
            data: {
                labels: this.labels,
                datasets: this.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                ...this.options
            }
        });

        this.$watch('labels', value => {
            this.chart.data.labels = value;
            this.chart.update();
        });

        this.$watch('datasets', value => {
            this.chart.data.datasets = value;
            this.chart.update();
        });

        // Handle resize
        window.addEventListener('resize', () => {
            const isNowMobile = window.innerWidth < 768;
            if (this.chart.options.plugins && this.chart.options.plugins.legend) {
                const currentPos = this.chart.options.plugins.legend.position;
                const newPos = isNowMobile ? 'bottom' : (this.options.plugins?.legend?.position || 'top');
                
                if (currentPos !== newPos) {
                    this.chart.options.plugins.legend.position = newPos;
                    this.chart.update();
                }
            }
        });
    }
}"
wire:ignore
class="w-full h-full min-h-[250px] max-h-[400px]"
{{ $attributes }}>
    <canvas x-ref="canvas"></canvas>
</div>
