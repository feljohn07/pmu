<div>
    <div class="grid gap-5 mb-4">
        <x-mary-button label="Randomize" wire:click="randomize" class="btn-primary" spinner />
        <x-mary-button label="Switch" wire:click="switchType" spinner />
    </div>

    <x-mary-chart :config="$chartConfig" wire:model="chartConfig" />
</div>