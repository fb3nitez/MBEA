@props(['title', 'step'])

<div class="card bg-base-100 border border-base-content/10 shadow-sm">
    <div class="card-body gap-4">
        <div class="flex items-center justify-between gap-4">
            <h3 class="card-title text-lg">{{ $title }}</h3>
            <button type="button" wire:click="goToStep({{ $step }})" class="btn btn-outline btn-sm">Edit</button>
        </div>
        <div class="space-y-3">{{ $slot }}</div>
    </div>
</div>
