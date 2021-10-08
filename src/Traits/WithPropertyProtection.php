<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Services\PropertyProtectionService;

/**
 * Enables property protection.
 */
trait WithPropertyProtection
{
    /**
     * Property protection service.
     *
     * @var PropertyProtectionService
     */
    protected PropertyProtectionService $propertyProtectionService;

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [];
}
