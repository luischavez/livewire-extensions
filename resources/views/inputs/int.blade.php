<input
    wire:model.debounce.300ms="value"
    wire:loading.attr.delay="disabled"
    class="w-full p-1 border rounded-md"
    type="number"
    min="{{ $inputRules['min'] ?? '' }}"
    max="{{ $inputRules['max'] ?? '' }}"
>
