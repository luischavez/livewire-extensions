<input
    wire:model.debounce.300ms="value"
    wire:loading.attr.delay="disabled"
    class="w-full p-1 border rounded-md"
    type="{{ $password ? 'password' : 'text' }}"
    placeholder="{{ $placeholder ?? '' }}"
    spellcheck="false"
    min="{{ $inputRules['min'] ?? '' }}"
    max="{{ $inputRules['max'] ?? '' }}"
>
