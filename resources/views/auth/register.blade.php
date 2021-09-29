<div class="flex flex-col items-center justify-center w-full h-full">
    <div class="w-4/5 lg:w-3/5 xl:w-2/5">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 text-center bg-black border-t-2">
                <h2 class="text-lg text-white">
                    {{ __('livewire-ext::auth.register_header') }}
                </h2>
            </div>
            <div class="flex flex-wrap p-2">
                <div class="flex flex-col w-full m-1">
                    <div class="flex flex-wrap items-center">
                        <div class="flex-auto w-1/5 min-w-min">
                            <label for="name">
                                @error('name')
                                    <span class="text-red-500">*</span>
                                @enderror
                                {{ __('livewire-ext::auth.name') }}
                            </label>
                        </div>
                        <div class="flex flex-auto w-4/5">
                            <input wire:model.debounce.500ms="name"
                                id="name"
                                class="w-full p-2 border-2 rounded-md @error('name') border-red-500 @enderror"
                                type="text">
                        </div>
                    </div>
                    @error('name')
                        <span class="p-1 text-sm text-right text-red-500 opacity-70">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="flex flex-col w-full m-1">
                    <div class="flex flex-wrap items-center">
                        <div class="flex-auto w-1/5 min-w-min">
                            <label for="email">
                                @error('email')
                                    <span class="text-red-500">*</span>
                                @enderror
                                {{ __('livewire-ext::auth.email') }}
                            </label>
                        </div>
                        <div class="flex flex-auto w-4/5">
                            <input wire:model.debounce.500ms="email"
                                id="email"
                                class="w-full p-2 border-2 rounded-md @error('email') border-red-500 @enderror"
                                type="email"
                                placeholder="email@domain.com">
                        </div>
                    </div>
                    @error('email')
                        <span class="p-1 text-sm text-right text-red-500 opacity-70">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="flex flex-col w-full m-1">
                    <div class="flex flex-wrap items-center">
                        <div class="flex-auto w-1/5 min-w-min">
                            <label for="password">
                                @error('password')
                                    <span class="text-red-500">*</span>
                                @enderror
                                {{ __('livewire-ext::auth.password') }}
                            </label>
                        </div>
                        <div class="flex flex-auto w-4/5">
                            <input wire:model.debounce.500ms="password"
                                id="password"
                                class="w-full p-2 border-2 rounded-md @error('password') border-red-500 @enderror"
                                type="password"
                                placeholder="******">
                        </div>
                    </div>
                    @error('password')
                        <span class="p-1 text-sm text-right text-red-500 opacity-70">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div class="flex flex-col w-full m-1">
                    <div class="flex flex-wrap items-center">
                        <div class="flex-auto w-1/5 min-w-min">
                            <label for="password_confirmation">
                                @error('password_confirmation')
                                    <span class="text-red-500">*</span>
                                @enderror
                                {{ __('livewire-ext::auth.confirm_password') }}
                            </label>
                        </div>
                        <div class="flex flex-auto w-4/5">
                            <input wire:model.debounce.500ms="password_confirmation"
                                id="password_confirmation"
                                class="w-full p-2 border-2 rounded-md @error('password_confirmation') border-red-500 @enderror"
                                type="password"
                                placeholder="******">
                        </div>
                    </div>
                    @error('password_confirmation')
                        <span class="p-1 text-sm text-right text-red-500 opacity-70">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
            <div class="flex flex-wrap justify-end p-2 space-x-1">
                <x-widgets-button wire:click="parent.close" type="default" class="text-sm">
                    {{ __('livewire-ext::auth.cancel') }}
                </x-widgets-button>
                <x-widgets-button wire:click="parent.execute" type="success" class="text-sm">
                    {{ __('livewire-ext::auth.register') }}
                </x-widgets-button>
            </div>
        </div>
    </div>
    <div class="mt-4 opacity-80">
        {{ __('livewire-ext::auth.already_registered') }}
        <button wire:click="parent.changePage('login')">{{ __('livewire-ext::auth.login') }}</button>
    </div>
</div>
