<div x-data="select()" x-init="init()">
    <select x-ref="select" class="hidden" {{ $attributes }}>
        {{ $slot }}
    </select>
    <div class="relative">
        <button type="button" @click="open = !open"
            class="x-select mt-2 block text-left px-3 w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6 bg-white"
            x-text="options.find(option => option.value === value)?.text ?? 'Select a venue...'">
        </button>
        <div x-show="open" @click.away="open = false" class="absolute mt-2 bg-white w-full rounded-md shadow-lg p-2 z-10"
            x-cloak>
            <input type="text"
                class="block text-left px-3 w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6 bg-white"
                placeholder="Search..." x-ref="searchInput"
                @keydown.window="if (event.keyCode === 191) $refs.searchInput.focus()" @keydown.escape="open = false"
                @input="search()">
            <ul class="max-h-[300px] overflow-y-auto mt-2">
                <template x-for="(option, index) in filteredOptions" :key="index">
                    <li>
                        <button type="button" @click="open = false; value = option.value;"
                            class="block text-left px-3 w-full rounded-md border-0 py-1.5 text-slate-900 placeholder:text-slate-400 sm:text-sm sm:leading-6 bg-white hover:bg-gray-100 cursor-pointer">
                            <span x-text="option.text"></span>
                        </button>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</div>

<style>
    .x-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
</style>

<script>
    function select() {
        return {
            open: false,
            options: [],
            filteredOptions: [],
            value: null,
            init() {
                this.options = [...this.$refs.select.options].map(option => {
                    return {
                        value: option.value,
                        text: option.innerText
                    };
                });
                this.filteredOptions = this.options;
                this.value = this.$refs.select.value;
                this.$watch('open', value => {
                    if (!value) {
                        this.$refs.searchInput.value = '';
                        this.search();
                    }
                });
                this.$watch('value', value => {
                    this.$refs.select.value = value;
                    this.$refs.select.dispatchEvent(new Event('change'));
                });
            },
            search() {
                this.filteredOptions = this.options.filter(option => {
                    return option.text.toLowerCase().includes(this.$refs.searchInput.value.toLowerCase());
                });
            }
        }
    }
</script>
