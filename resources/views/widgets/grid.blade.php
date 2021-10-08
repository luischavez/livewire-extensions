<div>
    @foreach ($items as $key => $item)
        @livewire($itemComponentName, ['item' => $item], key($key))
    @endforeach
</div>
