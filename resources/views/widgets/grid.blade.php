<div>
    @foreach ($data['items'] as $key => $item)
        {!! $this->gridableInstance()->output($key, $item) !!}
    @endforeach

    @if ($data['pages'] > 1)
        <div class="flex items-center justify-center my-5 space-x-1 text-sm">
            @if ($data['page'] > 1)
                <x-widgets-button wire:click="changePage(1)" icon="chevron-double-left">
                    First
                </x-widgets-button>
                <x-widgets-button wire:click="prevPage" icon="chevron-left">
                    Prev
                </x-widgets-button>
            @endif
            @if ($data['pages'] > 1)
                <div class="flex space-x-1">
                    @for ($i = $data['page'] - 2; $i <= $data['page'] + 2 && $i <= $data['pages']; $i++)
                        @if ($i < 1)
                            @continue
                        @endif
                        <x-widgets-button wire:click="changePage({{ $i }})" class="{{ $data['page'] == $i ? 'text-white bg-jaffa-500 hover:bg-jaffa-600 active:bg-jaffa-700' : '' }}">
                            {{ $i }}
                        </x-widgets-button>
                    @endfor
                </div>
            @endif
            @if ($data['page'] < $data['pages'])
                <x-widgets-button wire:click="nextPage" icon="chevron-right" :inverted="true">
                    Next
                </x-widgets-button>
                <x-widgets-button wire:click="changePage({{ $data['pages'] }})" icon="chevron-double-right" :inverted="true" class="text-white bg-jaffa-500">
                    Last
                </x-widgets-button>
            @endif
        </div>
    @endif
</div>
