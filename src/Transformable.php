<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Support\Facades\Crypt;
use Livewire\Wireable;
use Luischavez\Livewire\Extensions\Exceptions\TransformException;
use Luischavez\Livewire\Extensions\Utils\SecurityUtils;

/**
 * Base class for transformables.
 */
abstract class Transformable implements Wireable
{
    /**
     * Enables encryption.
     *
     * @var boolean
     */
    protected static bool $encrypted = false;

    /**
     * Transform the instance to a javascript value.
     */
    public abstract function toJavascript();

    /**
     * Create a new instance of this class from a javascript value.
     *
     * @param $value
     * @return self
     */
    public abstract static function fromJavascript($value): self;

    /**
     * @inheritDoc
     */
    public function toLivewire()
    {
        $value = $this->toJavascript();

        if (static::$encrypted) {
            $value = Crypt::encrypt($value);
        }

        SecurityUtils::throwIfInvalid($value);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public static function fromLivewire($value)
    {
        if (static::$encrypted) {
            $value = Crypt::decrypt($value);
        }

        $instance = static::fromJavascript($value);

        if ($instance === null) {
            throw new TransformException("Can't create a instance of ".(self::class)." with javascript value $value");
        }

        return $instance;
    }
}
