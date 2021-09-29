<div class="flex flex-col items-center justify-center w-full h-full">
    <div class="w-4/5 lg:w-3/5 xl:w-2/5">
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 text-center bg-black border-t-2">
                <h2 class="text-lg text-white">
                    {{ __('livewire-ext::auth.login_header') }}
                </h2>
            </div>
            <div class="flex flex-wrap p-2">
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
                <div class="flex items-center justify-end w-full m-1 space-x-1">
                    <input wire:model.debounce.500ms="remember"
                        id="remember"
                        class="p-2 border-2 rounded-md"
                        type="checkbox">    
                    <label class="select-none" for="remember">
                        {{ __('livewire-ext::auth.remember') }}
                    </label>
                </div>
            </div>
            <div class="flex flex-wrap justify-end p-2 space-x-1">
                <x-widgets-button wire:click="parent.close" type="default" class="text-sm">
                    {{ __('livewire-ext::auth.cancel') }}
                </x-widgets-button>
                <x-widgets-button wire:click="parent.execute" type="success" class="text-sm">
                    {{ __('livewire-ext::auth.login') }}
                </x-widgets-button>
            </div>
        </div>
    </div>
    <div class="mt-4 opacity-80">
        {{ __('livewire-ext::auth.new_user') }}
        <button wire:click="parent.changePage('register')">{{ __('livewire-ext::auth.create_account') }}</button>
    </div>
</div>
