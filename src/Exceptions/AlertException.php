<?php

namespace Luischavez\Livewire\Extensions\Exceptions;

use Exception;
use Luischavez\Livewire\Extensions\Builders\AlertBuilder;
use Luischavez\Livewire\Extensions\Widgets\Alert;

class AlertException extends Exception
{
    /**
     * Alerts.
     *
     * @var array
     */
    public array $alerts = [];

    /**
     * Spawner.
     *
     * @var string
     */
    public string $spawner = 'alerts';

    /**
     * Sets the spawner.
     *
     * @param string $spawner spawner
     * @return self
     */
    public function spawner(string $spawner): self
    {
        $this->spawner = $spawner;

        return $this;
    }

    /**
     * Add an alert.
     *
     * @param AlertBuilder $builder alert builder
     * @return self
     */
    public function alert(AlertBuilder $builder): self
    {
        $builder->type(Alert::DANGER)
            ->dimissable()
            ->closeAfter(5)
            ->withIcon('exclamation-circle');

        $this->alerts[] = $builder;

        return $this;
    }
}
