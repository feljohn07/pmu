<div>
    <form wire:submit.prevent="{{ $submitMethod }}">
        <div class="form-control mb-4">
            <label for="name" class="label">
                <span class="label-text">Name:</span>
            </label>
            <input type="text" id="name" wire:model="userForm.name" class="input input-bordered w-full" required />
        </div>

        <div class="form-control mb-4">
            <label for="email" class="label">
                <span class="label-text">Email:</span>
            </label>
            <input type="email" id="email" wire:model="userForm.email" class="input input-bordered w-full" required />
        </div>

        <div class="form-control mb-4">
            <label for="password" class="label">
                <span class="label-text">Password:</span>
            </label>
            <input type="password" id="password" wire:model="userForm.password" class="input input-bordered w-full" />
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="btn btn-primary">Submit</button>
            <x-mary-button label="Cancel" @click="$wire.showModal(false, null)" />
        </div>
    </form>
</div>
