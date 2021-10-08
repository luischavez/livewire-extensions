<?php

namespace Luischavez\Livewire\Extensions\Utils;

use Luischavez\Livewire\Extensions\Exceptions\SecurityException;

/**
 * Security utilities.
 */
class SecurityUtils
{
    /**
     * Validates the data and throw an error if not valid.
     *
     * @param mixed $data data
     * @return void
     * 
     * @throws SecurityException
     */
    public static function throwIfInvalid(mixed $data): void
    {
        if (is_array($data)) {
            foreach ($data as $value) {
                self::throwIfInvalid($value);
            }
        } else {
            if (!is_numeric($data)
                && !is_string($data)
                && !is_bool($data)
                && !is_null($data)) {
                $type = gettype($data);

                if ($type == 'object') {
                    $type = get_class($data);
                }

                throw new SecurityException("Invalid type: $type");
            }
        }
    }
}
