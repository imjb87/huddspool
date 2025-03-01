<div class="p-4">
    <h1 class="text-lg font-semibold tracking-tight text-slate-900">Override result</h1>
    <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit="override">
        <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-6 sm:gap-x-6">
            <div class="sm:col-span-6">
                <label for="homeScore" class="block text-sm font-medium leading-6 text-slate-900">{{$fixture->homeTeam->name}}</label>
                <input type="number" name="homeScore" id="homeScore" 
                    wire:model="homeScore"
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
            </div>
            <div class="sm:col-span-6">
                <label for="awayScore" class="block text-sm font-medium leading-6 text-slate-900">{{$fixture->awayTeam->name}}</label>
                <input type="number" name="awayScore" id="awayScore" 
                    wire:model="awayScore"
                    class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
            </div>
        </div>

        @if ($errors->any())
            <x-errors />
        @endif

        <div class="flex gap-x-3 pt-8 justify-end">
            <button type="submit"
                class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Override result</button>
        </div>        
    </form> 
</div>
