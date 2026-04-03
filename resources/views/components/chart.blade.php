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
    }
}"
wire:ignore
class="w-full h-full min-h-[250px]"
{{ $attributes }}>
    <canvas x-ref="canvas"></canvas>
</div>
