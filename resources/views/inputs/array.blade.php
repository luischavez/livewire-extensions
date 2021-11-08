<div x-data="{
        visibleItems: $wire.entangle('visibleItems'),
        closed: $wire.entangle('closed'),
        listHeight: 0,

        focusInput() {
            $refs.input.focus();
        },

        focusList() {
            $refs.list?.firstElementChild?.focus();
        },

        prev() {
            if (document.activeElement.previousElementSibling) {
                document.activeElement.previousElementSibling.focus();
            } else {
                this.focusInput();
            }
        },

        next() {
            document.activeElement.nextElementSibling?.focus();
        },

        setHeight() {
            if (this.listHeight != 0) return;

            let totalItems = $refs.list.childElementCount;

            let firstItem = $refs.list.firstElementChild;
            let itemHeight = firstItem.offsetHeight;

            this.listHeight = itemHeight * (this.visibleItems == 0 ? totalItems : this.visibleItems);
        },

        load() {
            $wire.load();
        },

        select(id) {
            $wire.select(id);
        },

        unselect(id) {
            $wire.unselect(id);
        },

        close() {
            this.closed = true;
        },

        open() {
            if (!this.closed) return;
            this.load();
        },

        toggle() {
            if (this.closed) {
                this.open();
            } else {
                this.close();
            }
        }
    }"
    @if ($value)
        x-init="$wire.triggerUpdate()"
    @endif
    class="w-full">
    <div class="relative flex flex-col w-full">
        <div class="relative flex w-full">
            <input
                x-ref="input"
                wire:model.debounce.300ms="searchTerm"
                wire:loading.attr.delay="disabled"
                x-on:keyup.escape="close()"
                x-on:click="toggle()"
                x-on:click.away="close()"
                x-on:keydown.arrow-down.prevent="focusList()"
                x-on:keyup.enter.stop.debounce.310ms="select(null)"
                @class([
                    'w-full p-1 border rounded-lg focus:outline-none',
                    'cursor-pointer' => !$searchEnabled,
                ])
                type="text"
                placeholder="{{ $placeholder ?? '' }}"
                spellcheck="false"
                @if ($multiple || !$searchEnabled) readonly @endif
            >

            @if ($multiple || !$searchEnabled)
                <x-widgets-icon x-on:click="toggle()"
                    :name="$closed ? 'chevron-down' : 'chevron-up'"
                    class="absolute right-0 px-1 cursor-pointer top-1/4" />
            @endif

            @if (!$closed && !empty($items))
                <ul x-ref="list"
                    x-init="setHeight()"
                    x-on:keydown.arrow-up.prevent="prev()"
                    x-on:keydown.arrow-down.prevent="next()"
                    x-on:keyup.escape="close()"
                    x-on:click.away="close()"
                    x-show="!closed"
                    x-transition
                    :style="`max-height: ${listHeight}px; scroll-behavior: auto;`"
                    class="absolute z-30 w-full overflow-hidden overflow-y-visible bg-white rounded-lg shadow-md top-full">
                    @foreach ($items as $id => $view)
                        <li wire:key='item_{{ $id }}'
                            x-on:keyup.enter.stop="select('{{ $id }}')"
                            x-on:click="select('{{ $id }}')"
                            x-on:click="select()"
                            class="px-2 py-1 cursor-pointer hover:bg-blue-500 hover:text-white focus:bg-blue-500 focus:text-white"
                            tabindex="{{ $loop->index }}">
                            {!! $view !!}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if ($multiple && !empty($value) && is_array($value))
            <div class="flex flex-wrap p-1 space-x-1 text-xs">
                @foreach ($value as $id => $text)
                    <span class="flex items-center p-1 space-x-1 text-black bg-white border rounded-md">
                        {{ $text }}
                        <x-widgets-icon x-on:click="unselect({{ $id }})"
                            name="x"
                            class="text-red-500 cursor-pointer" />
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</div>
