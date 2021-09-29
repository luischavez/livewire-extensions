<div x-data="{
        type: $wire.entangle('type'),
        closeAfter: $wire.entangle('closeAfter'),
        elapsedTime: 0,
        remainingVisiblePercent: 100,
        interval: null,
        show: false,

        dimiss() {
            this.show = false;

            if (this.interval !== null) {
                clearInterval(this.interval);
            }

            $el.parentElement.remove();
        },

        countdown() {
            this.show = true;

            if (this.closeAfter == 0) return;

            this.interval = setInterval(() => {
                this.elapsedTime += 100;
                this.remainingVisiblePercent = 100 - (this.elapsedTime * 100) / (this.closeAfter * 1000);

                if (this.elapsedTime >= (this.closeAfter * 1000)) {
                    this.dimiss();
                }
            }, 100);
        },

        cancel() {
            $wire.cancel().then(() => this.dimiss());
        },

        confirm() {
            $wire.confirm().then(() => this.dimiss());
        }
    }"
    x-init="countdown()"
    x-transition.scale.50
    x-cloak
    x-show="show"
    class="flex bg-white rounded-lg shadow-lg max-w-max">
    <div :class="{
            'bg-black'      : type == 'default' || type == '',
            'bg-green-500'  : type == 'success',
            'bg-yellow-500' : type == 'warning',
            'bg-red-500'    : type == 'danger',
            'bg-blue-500'   : type == 'info'
        }"
        class="flex items-center px-2 text-xl text-white rounded-tl-lg rounded-bl-lg">
        @if ($iconName)
            <x-widgets-icon :name="$iconName" :style="$iconStyle" :group="$iconGroup" />
        @endif
    </div>
    <div class="relative flex flex-col max-w-lg space-y-1">
        <div class="relative flex mr-2">
            <div x-ref="countdown"
                :class="{
                    'bg-black'      : type == 'default' || type == '',
                    'bg-green-500'  : type == 'success',
                    'bg-red-500'    : type == 'danger',
                    'bg-yellow-500' : type == 'warning',
                    'bg-blue-500'   : type == 'info'
                }"
                :style="`width: ${remainingVisiblePercent}%`"
                class="absolute top-0 left-0 h-0.5 transition-all"></div>
        </div>
        <div class="max-w-md min-w-full p-1 bg-white">
            @if ($dimissable)
                <div class="flex justify-end text-sm">
                    <x-widgets-icon x-on:click="dimiss" name="x" class="cursor-pointer" />
                </div>
            @endif
            <div class="text-sm">
                <h3 :class="{
                        'text-black'      : type == 'default' || type == '',
                        'text-green-500'  : type == 'success',
                        'text-red-500'    : type == 'danger',
                        'text-yellow-500' : type == 'warning',
                        'text-blue-500'   : type == 'info',
                    }"
                    class="font-bold">
                    <span class="break-words">
                        {{ $title }}
                    </span>
                </h3>
            </div>
            <p class="p-1 text-xs">
                <p class="overflow-scroll text-xs break-words max-h-40">
                    {!! $message !!}
                </p>

                @if ($inputName)
                    <div class="w-full">
                        <livewire:widgets-smart-input :input="$inputName"
                            :value="$inputValue"
                            :options="$inputOptions"
                            :input-callback="callback($this)->toSelf('input')" />
                    </div>
                @endif
            </p>
        </div>

        <div class="flex justify-end px-2 py-1 space-x-2 text-xs">
            @if ($showCancelButton)
                <button x-on:click="cancel"
                    class="text-black">
                    {{ $cancelText ?? __('livewire-ext::alert.cancel') }}
                </button>
            @endif
            @if ($showConfirmButton)
                <button x-on:click="confirm"
                    :class="{
                        'text-black'      : type == 'default' || type == '',
                        'text-green-500'  : type == 'success',
                        'text-red-500'    : type == 'danger',
                        'text-yellow-500' : type == 'warning',
                        'text-blue-500'   : type == 'info',
                    }"
                    class="font-bold"
                    :disabled="$inputName && in_array('required', $inputOptions['rules'] ?? []) && empty($inputValue)">
                    {{ $confirmText ?? __('livewire-ext::alert.confirm') }}
                </button>
            @endif
        </div>
    </div>
</div>
