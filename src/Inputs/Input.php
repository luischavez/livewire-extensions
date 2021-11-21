<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Luischavez\Livewire\Extensions\Proxy;
use Throwable;

/**
 * Base input.
 */
abstract class Input extends Proxy
{
    /**
     * Input value.
     *
     * @var mixed
     */
    public mixed $value = null;

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'value' => $this->rules,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getValidationAttributes(): array
    {
        return trans('livewire-ext::input.attributes');
    }

    /**
     * @inheritDoc
     */
    public function updated(string $key, mixed $value): void
    {
        $this->validateOnly($key);
    }

    /**
     * Check if the input is valid.
     *
     * @return boolean
     */
    protected function isValid(): bool
    {
        try {
            $this->validate();
        } catch (Throwable $ex) {
            return false;
        }

        return true;
    }

    /**
     * Gets the input value.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Extra values to add on input callback.
     *
     * @return array
     */
    public function extraValues(): array
    {
        return [];
    }

    /**
     * Gets the input return values for the input callback.
     *
     * @return array
     */
    public function values(): array
    {
        $value = $this->value();

        if (!$this->isValid()) {
            $value = null;
        }

        return array_merge([$value], $this->extraValues());
    }
}
