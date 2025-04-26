<x-app-layout>
    <div class="mx-20 mt-10">
        <p class="text-xl font-semibold mb-5">Projects Overview</p>

        <div class="flex flex-wrap gap-6 justify-center lg:justify-start">
            <livewire:project-status-chart-component category="" />
            <livewire:project-pow-status-barchart category="" />
        </div>
        <livewire:project-manager projectCategory='' />
    </div>
</x-app-layout>