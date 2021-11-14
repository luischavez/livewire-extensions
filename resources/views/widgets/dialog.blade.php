<div x-data="{
        type: $wire.entangle('type'),
        dimissable: $wire.entangle('dimissable'),

        dimiss() {
            $el.remove();
        },

        cancel() {
            $wire.cancel().then(() => this.dimiss());
        },

        confirm() {
            $wire.confirm().then(() => this.dimiss());
        }
    }"
    class="fixed top-0 left-0 z-40 w-full h-screen">
    <div class="flex items-center justify-center h-full">
        <div x-data
            x-on:click.away="
                if (dimissable && $event.srcElement.firstElementChild == $el) {
                    dimiss();
                }
            "
            class="w-2/5 bg-white rounded-md shadow-xl lg:w-2/5 xl:w-1/5">
            <div
                :class="{
                    'bg-white text-black':      type == 'default' || type == '',
                    'bg-green-500 text-white':  type == 'success',
                    'bg-yellow-500 text-white': type == 'warning',
                    'bg-red-500 text-white':    type == 'danger',
                    'bg-blue-500 text-white':   type == 'info'
                }"
                class="flex items-center px-2 py-1 space-x-1 text-lg rounded-t-md">
                @if ($iconName)
                    <x-widgets-icon :name="$iconName" :style="$iconStyle" :group="$iconGroup" />
                @endif
                <span>
                    {{ $title }}
                </span>
            </div>
            <div class="flex flex-col p-2 space-y-1">
                <span class="break-words">
                    {!! $message !!}
                </span>
                @if ($inputName)
                    <div class="w-full">
                        <livewire:widgets-smart-input :input="$inputName"
                            :value="$inputValue"
                            :options="$inputOptions"
                            :input-callback="callback($this)->toSelf('input')" />
                    </div>
                @endif
                <div class="flex justify-end mt-2 space-x-1 text-sm">
                    @if ($showCancelButton)
                        <x-widgets-button x-on:click="cancel" type="default" class="text-sm">
                            {{ $cancelText ?? __('livewire-ext::dialog.cancel') }}
                        </x-widgets-button>
                    @endif
                    @if ($showConfirmButton)
                        <x-widgets-button x-on:click="confirm"
                            :type="$type"
                            class="text-sm"
                            :disabled="$inputName && in_array('required', $inputOptions['rules'] ?? []) && empty($inputValue)">
                            {{ $confirmText ?? __('livewire-ext::dialog.confirm') }}    
                        </x-widgets-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
