<div>
    <div
        x-data="{
            width: 0,
            itemWidth: 0,

            gridResizeObserver: null,
            resizer: null,

            handleResize(newWidth) {
                if (this.width == newWidth) {
                    return;
                }

                this.width = newWidth;

                if (this.resizer !== null) {
                    clearTimeout(this.resizer);
                }

                this.resizer = setTimeout(() => {
                    this.itemWidth = $el.firstElementChild.getClientRects()[0].width;

                    $wire.fillGrid(this.width, this.itemWidth);
                }, 500);
            },

            onGridResize(entry) {
                let size = entry.contentBoxSize.flat().pop();
                
                let newWidth = size.inlineSize;

                this.handleResize(newWidth);
            }
        }"
        x-init="
            itemWidth = $el.firstElementChild.getClientRects()[0].width;

            gridResizeObserver = new ResizeObserver((entries) => onGridResize(entries[0]));
            gridResizeObserver.observe($el);
        "
        :style="`display: grid; grid-column-gap: {{ $gap }}px; grid-template-columns: repeat(auto-fit, ${itemWidth}px);`"
    >
        @foreach ($items as $item)
            <div class="max-w-max min-w-min">
                {!! $this->gridableInstance()->output($item) !!}
            </div>
        @endforeach
    </div>

    <div>
        {{ $page }} of {{ $pages }}
    </div>

    @if ($pages > 1)
        <div class="flex items-center justify-center my-5 space-x-1 text-sm">
            @if ($page > 1)
                <x-widgets-button wire:click="changePage(1)" icon="chevron-double-left" title="Page 1">
                    First
                </x-widgets-button>
                <x-widgets-button wire:click="prevPage" icon="chevron-left" title="Page {{ $page - 1 }}">
                    Prev
                </x-widgets-button>
            @endif
            @if ($pages > 1)
                <div class="flex space-x-1">
                    @for ($i = $page - 2; $i <= $page + 2 && $i <= $pages; $i++)
                        @if ($i < 1)
                            @continue
                        @endif
                        <x-widgets-button wire:click="changePage({{ $i }})" class="{{ $page == $i ? 'text-white bg-jaffa-500 hover:bg-jaffa-600 active:bg-jaffa-700' : '' }}">
                            {{ $i }}
                        </x-widgets-button>
                    @endfor
                </div>
            @endif
            @if ($page < $pages)
                <x-widgets-button wire:click="nextPage" icon="chevron-right" :inverted="true" title="Page {{ $page + 1 }}">
                    Next
                </x-widgets-button>
                <x-widgets-button wire:click="changePage({{ $pages }})" icon="chevron-double-right" :inverted="true" class="text-white bg-jaffa-500" title="Page {{ $pages }}">
                    Last
                </x-widgets-button>
            @endif
        </div>
    @endif
</div>
