<div class="flex-1">
    <div class="mx-auto max-w-3xl py-10 px-4 sm:px-6 lg:py-12 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Edit Section</h1>
        @if ($errors->any())
            <x-errors />
        @endif
        <form class="divide-y-slate-200 mt-6 space-y-8 divide-y" wire:submit.prevent="save">
            <div class="grid grid-cols-1 gap-y-6 pt-8 sm:grid-cols-6 sm:gap-x-6">

                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Name</label>
                    <input type="text" name="name" id="name" autocomplete="name"
                        wire:model.defer="section.name"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6" />
                </div>

                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Ruleset</label>
                    <select name="ruleset_id" id="ruleset_id" wire:model.defer="section.ruleset_id"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6">
                        <option value="">Select a ruleset</option>
                        @foreach ($rulesets as $ruleset)
                            <option value="{{ $ruleset->id }}">{{ $ruleset->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-6">
                    <label for="teams[]" class="block text-sm font-medium leading-6 text-slate-900">Teams</label>
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
                    
                        <input type="hidden" name="teams" wire:model="section_teams">
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
                </div>
            </div>

            <div class="flex gap-x-3 pt-8 justify-end">
                <a href="{{ route('admin.sections.show', $section) }}"
                    class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md bg-blue-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Save</button>
            </div>
        </form>
    </div>
    <script>
        function sortableList() {
          return {
            selectedItem: '',
            items: [],
            dragIndex: null,
      
            addItem() {
              // Check if the item is already in the list
              if (this.items.some(item => item.id === this.selectedItem)) return;
      
              // Find the selected option and add it to the list
              const selectedOption = document.querySelector(`[value="${this.selectedItem}"]`);
              if (selectedOption) {
                this.items.push({
                  id: this.selectedItem,
                  name: selectedOption.innerText
                });
              }
            },
      
            onDragEnter(index) {
              if (this.dragIndex === null || this.dragIndex === index) return;
              const draggedItem = this.items[this.dragIndex];
              this.items.splice(this.dragIndex, 1);
              this.items.splice(index, 0, draggedItem);
              this.dragIndex = index;
            },
      
            onDrop(index) {
              if (this.dragIndex === null) return;
              this.dragIndex = null;
            }
          };
        }
      </script>
<style>
    .droppable-list {
      display: grid;
      grid-template-columns: repeat(1, 1fr);
      gap: 10px;
      list-style: none;
      padding: 0;
    }
  
    .droppable-list li {
      background-color: #f0f0f0;
      padding: 10px;
      border: 1px solid #ccc;
    }
  
    .empty-droppable {
      background-color: #e0e0e0;
    }
  </style>      
</div>
