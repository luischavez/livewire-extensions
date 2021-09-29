<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Livewire\HydrationMiddleware\HydratePublicProperties;
use Throwable;

/**
 * Enables view data protection.
 */
trait WithViewDataProtection
{
    /**
     * Hidden data when user not logged.
     *
     * @var string|null
     */
    public ?string $hiddenAuthData = null;

    /**
     * Protected properties.
     *
     * @return array
     */
    protected function protectPropertiesWithViewDataProtection(): array
    {
        return [
            'hiddenAuthData',
        ];
    }

    /**
     * Encrypts the data of this component if hideDataWhenNotLogged enabled.
     *
     * @return void
     */
    public function hydrateWithViewDataProtection(): void
    {
        if (auth()->check() && !empty($this->hiddenAuthData)) {
            $memo = Crypt::decrypt($this->hiddenAuthData);
            $memo = json_decode($memo, true);

            $request = app()->make(Request::class);
            $request->memo = $memo;

            HydratePublicProperties::hydrate($this, $request);

            $this->hiddenAuthData = null;
        }
    }

    /**
     * Decrypts the data of this component if hideDataWhenNotLogged enabled.
     *
     * @return void
     */
    public function dehydrateWithViewDataProtection(): void
    {
        if (!auth()->check() && empty($this->hiddenAuthData)) {
            $response = app()->make(Response::class);
            HydratePublicProperties::dehydrate($this, $response);

            $memo = $response->memo;

            foreach ($memo['data'] as $key => $value) {
                if ($key == 'as') continue;

                try {
                    $this->{$key} = null;
                } catch (Throwable $ex) {
                    if (is_string($this->{$key})) {
                        $this->{$key} = '';
                    } else if (is_array($this->{$key})) {
                        $this->{$key} = [];
                    } else if (is_numeric($this->{$key})) {
                        $this->{$key} = 0;
                    }
                }
            }

            $data = json_encode($memo);
            $data = Crypt::encrypt($data);

            $this->hiddenAuthData = $data;
        }
    }
}
