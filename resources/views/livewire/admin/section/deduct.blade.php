<div class="p-4">
    <h1 class="text-lg font-semibold tracking-tight text-slate-900">Deduct points</h1>
    <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit.prevent="deduct">
        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
            <div class="sm:col-span-6">
                <label for="deducted" class="block text-sm font-medium leading-6 text-slate-900">Deducted points</label>
                <input type="number" name="deducted" id="deducted" 
                    wire:model.defer="deducted"
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
            </div>
        </div>
        <div class="flex gap-x-3 pt-8 justify-end">
            <button type="submit"
                class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Deduct points</button>
        </div>        
    </form> 
</div>
