<x-app-layout>
    <div class="mx-20 mt-10">
        <p class="text-xl font-semibold mb-5">Projects Overview</p>

        <div class="flex flex-wrap gap-6 justify-center lg:justify-start">
            <livewire:project-status-chart-component category="repairs" />
            <livewire:project-pow-status-barchart category="repairs" />
        </div>
        <livewire:project-manager projectCategory='repairs' />
    </div>
</x-app-layout>