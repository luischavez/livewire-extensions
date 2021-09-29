<div wire:ignore
    x-data="{
        parentId: '{{ $parentId }}',
        blurBackground: {{ $blurBackground ? 'true' : 'false' }},
        disableBackgroundEvents: {{ $disableBackgroundEvents ? 'true' : 'false' }},
        viewIds: [],

        show(view) {
            if (typeof view === 'undefined') return;

            let fragment = document.createDocumentFragment();
            let div = document.createElement('div');

            div.innerHTML = view;

            let wireId = div.firstElementChild.attributes['wire:id'].value;

            fragment.appendChild(div);

            $el.appendChild(fragment);

            window.Livewire.rescan($el);
            this.viewIds.push(wireId);
        },

        remove(wireId) {
            let component = window.Livewire.components.componentsById[wireId];

            for (let childId of component.childIds) {
                this.remove(childId);
            }

            delete window.Livewire.components.componentsById[wireId];

            let index = this.viewIds.indexOf(wireId);
            if (index > -1) {
                this.viewIds.splice(index, 1);
            }
        },
    }"
    x-init="
        $watch('viewIds', (viewIds) => {
            if (!parentId) return;

            if (viewIds.length > 0) {
                if (blurBackground) {
                    document.getElementById(parentId).classList.add('bg-gray-500');
                    document.getElementById(parentId).classList.add('opacity-10');
                }
                
                if (disableBackgroundEvents) {
                    document.getElementById(parentId).classList.add('pointer-events-none');
                }
            } else {
                if (blurBackground) {
                    document.getElementById(parentId).classList.remove('bg-gray-500');
                    document.getElementById(parentId).classList.remove('opacity-10');
                }
                
                if (disableBackgroundEvents) {
                    document.getElementById(parentId).classList.remove('pointer-events-none');
                }
            }
        });

        $el.addEventListener('DOMNodeRemoved', (event) => {
            if (event.target && event.target.attributes) {
                let wireId = event.target.attributes['wire:id']?.value;
            
                if (typeof wireId !== 'undefined') {
                    remove(wireId);
                }
            }
        }, false);
    "
    x-on:spawn-{{ $name }}.window="show($event.detail.view)"
    {{ $attributes->merge(['class' => '']) }}>
</div>
