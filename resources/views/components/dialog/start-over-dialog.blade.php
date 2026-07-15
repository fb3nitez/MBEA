<dialog id="start_over_dialog" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Start Over!</h3>
        <p class="pt-4 pb-2">
            This will clear all the information you've entered and return you to the first step.
        </p>
        <p class="py-2 text-error">
            <b>Warning:</b> This action cannot be undone!
        </p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Cancel</button>
            </form>
            <button class="btn btn-error" wire:click="startOver">Sure</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
