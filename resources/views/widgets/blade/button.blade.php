@if (isset($icon))
    <button type="button"
        class="overflow-hidden bg-white rounded-md shadow-md cursor-pointer select-none active:shadow-none disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-gray-400"
        {{ $disabled ? 'disabled' : '' }}>
        <div {{ $attributes->class([
            'flex border rounded-md overflow-hidden',
            'flex-row-reverse' => !$inverted,
        ])->merge() }}>
            <span @class([
                'flex items-center p-1 text-black bg-white hover:bg-gray-50 active:bg-gray-200'
            ])>
                {!! $slot !!}
            </span>
            <div @class([
                'flex items-center p-1',
                'rounded-tr-md rounded-br-md'   => $inverted,
                'rounded-tl-md rounded-bl-md'   => !$inverted,
                'bg-white text-black'           => $type == 'default',
                'bg-green-500 text-white'       => $type == 'success',
                'bg-yellow-500 text-white'      => $type == 'warning',
                'bg-red-500 text-white'         => $type == 'danger',
                'bg-blue-500 text-white'        => $type == 'info',
            ])>
                <x-widgets-icon :name="$icon" :group="$group" />
            </div>
        </div>
    </button>
@else
    <button type="button"
        {{
            $attributes->class([
                'flex border rounded-md shadow-md active:shadow-none select-none cursor-pointer bg-white disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-gray-400',
                'bg-white hover:bg-gray-50 active:bg-gray-200 text-black'           => $type == 'default' || empty($type),
                'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white'    => $type == 'success',
                'bg-yellow-500 hover:bg-yellow-600 active:bg-yellow-700 text-white' => $type == 'warning',
                'bg-red-500 hover:bg-red-600 active:bg-red-700 text-white'          => $type == 'danger',
                'bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white'       => $type == 'info',
            ])->merge()
        }}
        {{ $disabled ? 'disabled' : '' }}>
        <span class="p-1">
            {!! $slot !!}
        </span>
    </button>
@endif
