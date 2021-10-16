<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Luischavez\Livewire\Extensions\Exceptions\AlertException;
use Luischavez\Livewire\Extensions\Services\AlertService;
use Luischavez\Livewire\Extensions\Traits\WithActions;
use Luischavez\Livewire\Extensions\Traits\WithAlerts;
use Luischavez\Livewire\Extensions\Traits\WithCallbacks;
use Luischavez\Livewire\Extensions\Traits\WithDeclarativeListeners;
use Luischavez\Livewire\Extensions\Traits\WithDialogs;
use Luischavez\Livewire\Extensions\Traits\WithPropertyProtection;
use Luischavez\Livewire\Extensions\Traits\WithServices;
use Luischavez\Livewire\Extensions\Traits\WithSpawns;
use Luischavez\Livewire\Extensions\Traits\WithTag;
use Luischavez\Livewire\Extensions\Widgets\Alert;
use Throwable;

/**
 * Extended Livewire component.
 */
class ExtendedComponent extends Component
{
    use WithServices,
        WithTag,
        WithActions,
        WithCallbacks,
        WithAlerts,
        WithDialogs,
        WithDeclarativeListeners,
        WithPropertyProtection,
        WithSpawns;

    /**
     * Log and show the alerts if required.
     *
     * @param Throwable $ex exception
     * @return void
     */
    protected function logAndShowAlerts(Throwable $ex): void
    {
        if ($ex instanceof ValidationException) {
            throw $ex;
        }

        $showOnError = config('livewire-ext.alerts.show_on_error');

        if (!$showOnError) {
            throw $ex;
        }

        Log::error($ex);

        $alerts = [];

        if ($ex instanceof AlertException) {
            $alerts = $ex->alerts;
        }

        if (empty($alerts)) {
            $debug = config('app.debug');

            $title = $debug
                ? "{$ex->getFile()}:{$ex->getLine()}"
                : trans('livewire-ext::alert.throw.title');
            $message = $debug
                ? $ex->__toString()
                : trans('livewire-ext::alert.throw.message');

            /**
             * @var AlertService
             */
            $alertService = AlertService::of($this, true);

            $alerts[] = $alertService->alert($title, $message)
                ->type(Alert::DANGER);
        }

        /**
         * @var AlertBuilder
         */
        foreach ($alerts as $alert) {
            $alert->dimissable()->closeAfter(0)->show();
        }
    }

    /**
     * @inheritDoc
     */
    public function callMethod($method, $params = [], $captureReturnValueCallback = null)
    {
        try {
            parent::callMethod($method, $params, $captureReturnValueCallback);
        } catch (Throwable $ex) {
            $this->logAndShowAlerts($ex);
        }
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Container $container, Route $route)
    {
        try {
            return parent::__invoke($container, $route);
        } catch (Throwable $ex) {
            $this->logAndShowAlerts($ex);
        }
    }

    /**
     * @inheritDoc
     */
    public function __call($method, $params)
    {
        try {
            return parent::__call($method, $params);
        } catch (Throwable $ex) {
            $this->logAndShowAlerts($ex);
        }
    }
}
