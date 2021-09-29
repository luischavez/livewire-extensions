<div class="flex flex-col space-y-1">
    <div class="flex">
        {!! $proxyView !!}
    </div>
    @error('proxyData.value')
        <span class="p-1 text-sm text-right text-red-500 opacity-70">
            {{ $message }}
        </span>
    @enderror
</div>
