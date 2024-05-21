<x-filament-widgets::widget>
    <x-filament::section>

    <script>import Chart from 'chart.js/auto';
    </script>
    <div
        x-data="{}"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('index-script'))]"
        >
            <canvas id="myChart"></canvas>
            <button
                id="button1"
                type="button"
                style="
                    border: white;
                    color:blueviolet;
                "
            >Click me</button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
