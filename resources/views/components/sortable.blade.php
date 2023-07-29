<div x-data="sortable">
    <div>
        <select x-ref="select">
            <option value="">Select an option...</option>
            <option value="1">Option 1</option>
            <option value="2">Option 2</option>
        </select>
        <button type="button" @click="addItem()">Add</button>
    </div>

    <div
        @drop.prevent="if(dragging !== null &amp;&amp; dropping !== null){if(dragging &lt; dropping) a = [...a.slice(0, dragging), ...a.slice(dragging + 1, dropping + 1), a[dragging], ...a.slice(dropping + 1)]; else a = [...a.slice(0, dropping), a[dragging], ...a.slice(dropping, dragging), ...a.slice(dragging + 1)]}; dropping = null;"
        @dragover.prevent="$event.dataTransfer.dropEffect = &quot;move&quot;">
        <div> 
            <template x-for="(i, index) in a" :key="index">
                <div class="flex mt-2 px-3 w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm  placeholder:text-slate-400 sm:text-sm sm:leading-6 relative" draggable="true"
                    :class="{ 'border-blue-600': dragging === index, 'bg-gray-200': !i.name, 'bg-white ring-1 ring-inset ring-slate-300': i.name }"
                    @dragstart="dragging = index"
                    @dragend="dragging = null">
                    <span class="whitespace-nowrap text-xs font-semibold text-slate-900 w-8 sm:leading-6" x-text="index + 1"></span>
                    <div class="ml-2"><span x-text="i.name"></span></div>
                    <div class="absolute inset-0 opacity-50" x-show.transition="dragging !== null"
                        :class="{ 'bg-blue-200': dropping === index }"
                        @dragenter.prevent="if(index !== dragging) {dropping = index}"
                        @dragleave="if(dropping === index) dropping = null"></div>
                    <i class="fas fa-grip-vertical cursor-move ml-auto sm:leading-6 text-gray-500" x-show="i.name"></i>
                </div>
            </template>
        </div>
    </div>

    <input type="hidden" name="teams" x-model="JSON.stringify(a.map(i => i.id))" wire:model="teams">
    <script>
        function sortable() {
            return {
                a: [],
                dragging: null,
                dropping: null,
                init() {
                    for (var i = 1; i <= 10; i++) {
                        this.a.push({
                            name: '',
                            id: ''
                        });
                    };
                },
                addItem() {
                    for (var i = 0; i < this.a.length; i++) {
                        if (this.a[i].name === '') {
                            this.a[i].name = this.$refs.select.options[this.$refs.select.selectedIndex].innerText;
                            this.a[i].id = this.$refs.select.value;
                            break;
                        }
                    }
                }
            }
        }
    </script>
</div>
