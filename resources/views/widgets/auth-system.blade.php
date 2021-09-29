<div x-data="{
        show: $wire.entangle('show'),
        modal: $wire.entangle('modal'),
        parentId: $wire.entangle('parentId')
    }"
    x-init="
        $watch('show', () => {
            if (!parentId) return;

            if (show) {
                document.getElementById(parentId).classList.add('bg-gray-500');
                document.getElementById(parentId).classList.add('opacity-10');
                document.getElementById(parentId).classList.add('pointer-events-none');
            } else {
                document.getElementById(parentId).classList.remove('bg-gray-500');
                document.getElementById(parentId).classList.remove('opacity-10');
                document.getElementById(parentId).classList.remove('pointer-events-none');
            }
        });
    "
    x-show="show || !modal"
    x-cloak
    x-on:auth-request.window="
        if ($event.detail == 'logout') {
            $wire.logout();
        } else {
            $wire.changePage($event.detail);
        }
    "
    class="fixed top-0 left-0 z-30 w-full h-screen">

    @auth
        @if ($timeout > 0)
            <div wire:poll.5000ms="check" class="hidden"></div>
        @endif
    @endauth

    @if ($redirecting)
        <div x-data="{
                redirectAfter: $wire.entangle('redirectAfter')
            }"
            x-init="
                setInterval(() => {
                    if (redirectAfter > 0 && --redirectAfter == 0) {
                        $wire.redirectBack();
                    }
                }, 1000);
            ">
            redirecting in <span x-text="redirectAfter"></span> seconds
        </div>
    @else
        <div class="flex items-center justify-center h-full">
            {!! $proxyView !!}
        </div>
    @endif
</div>
