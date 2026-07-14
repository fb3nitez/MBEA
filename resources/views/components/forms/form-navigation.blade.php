<div class="flex flex-col gap-3 border-t border-base-content/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
    @if($currentStep > 1)
        <button type="button" wire:click="prevStep" class="btn btn-outline" id="btn-back">
            Back
        </button>
    @endif

    <div class="ml-auto flex w-full gap-3 sm:w-auto sm:justify-end">
        @if($currentStep < $totalSteps)
            <button type="button" wire:click="nextStep" class="btn btn-primary" id="btn-next">
                Next
            </button>
        @endif

        @if($currentStep === $totalSteps)
            <button type="button" wire:click="submit" class="btn btn-success" id="btn-submit" wire:loading.attr="disabled">
                <span wire:loading.remove>Submit</span>
                <span wire:loading>
                    <span class="loading loading-spinner loading-sm"></span>
                    Submitting...
                </span>
            </button>
        @endif
    </div>
</div>
