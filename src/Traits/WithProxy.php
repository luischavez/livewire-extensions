<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Illuminate\Contracts\View\View;
use Luischavez\Livewire\Extensions\Services\ProxyService;

/**
 * Enables proxies.
 */
trait WithProxy
{
    /**
     * Proxy service.
     *
     * @var ProxyService
     */
    protected ProxyService $proxyService;

    /**
     * Available proxies.
     *
     * @var array
     */
    protected array $proxies = [];

    /**
     * Current proxy name.
     *
     * @var string|null
     */
    public ?string $proxyName = null;

    /**
     * Proxy data.
     *
     * @var array
     */
    public array $proxyData = [];

    /**
     * Change the proxy.
     *
     * @param string    $proxyName      name of the proxy
     * @param array     $initialData    initial data for the proxy
     * @return void
     */
    protected function changeProxy(string $proxyName, array $initialData = []): void
    {
        $this->proxyService->change($proxyName, $initialData);
    }

    /**
     * Protected properties.
     *
     * @return array
     */
    protected function protectPropertiesWithProxy(): array
    {
        return ['proxyName'];
    }

    /**
     * Calls an internal proxy method.
     *
     * @param string    $methodName     method name
     * @param     ...$parameters  parameters
     * 
     */
    protected function callProxyInternalMethod(string $methodName, ...$parameters)
    {
        return $this->proxyService->callProxyInternalMethod($methodName, ...$parameters);
    }

    /**
     * Calls a proxy method.
     *
     * @param string    $methodName     method name
     * @param     ...$parameters  parameters
     * 
     */
    public function callProxyMethod(string $methodName, ...$parameters)
    {
        return $this->proxyService->callProxyMethod($methodName, ...$parameters);
    }

    /**
     * @inheritDoc
     */
    public function renderToView()
    {
        /**
         * @var View
         */
        $view = parent::renderToView();

        return $this->proxyService->render($view);
    }

    /**
     * Handles proxy callbacks.
     *
     * @param string    $name       proxy name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * 
     */
    protected function handleProxyCallbackEvent(string $name, string $event, array $parameters)
    {
        return $this->proxyService->handleProxyCallbackEvent($name, $event, $parameters);
    }
}
