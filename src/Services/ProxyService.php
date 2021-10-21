<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Luischavez\Livewire\Extensions\Exceptions\ProxyException;
use Luischavez\Livewire\Extensions\Proxy;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Reflection\Method;
use Luischavez\Livewire\Extensions\Reflection\Property;
use Luischavez\Livewire\Extensions\Utils\SecurityUtils;

/**
 * Proxy service.
 */
class ProxyService extends LivewireService
{
    /**
     * Proxy instance.
     *
     * @var Proxy|null
     */
    protected ?Proxy $proxy = null;

    /**
     * Sets the proxy data in the proxy instance.
     *
     * @param array $data array of data
     * @return void
     */
    protected function setProxyInstanceData(array $data): void
    {
        foreach ($data as $name => $value) {
            $this->proxy->$name = $value;
        }
    }

    /**
     * Gets the proxy instance data.
     *
     * @return array
     */
    protected function getProxyInstanceData(): array
    {
        $data = [];

        $properties = Inspector::inspect($this->proxy)
            ->property()
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->all();

        /**
         * @var Property
         */
        foreach ($properties as $property) {
            $name = $property->name();
            $value = $property->value();

            $data[$name] = $value;
        }

        SecurityUtils::throwIfInvalid($data);

        return $data;
    }

    /**
     * Set the proxy data in the component.
     *
     * @param array $data array of data
     * @return void
     */
    protected function setProxyArrayData(array $data): void
    {
        $this->component->proxyData = [];

        foreach ($data as $name => $value) {
            $this->component->proxyData[$name] = $value;
        }
    }

    /**
     * Gets the proxy array data from the component.
     *
     * @return array
     */
    protected function getProxyArrayData(): array
    {
        $data = [];

        foreach ($this->component->proxyData as $name => $value) {
            $data[$name] = $value;
        }

        return $data;
    }

    /**
     * Verifies if the data is valid for the proxy.
     *
     * @param array $data array of data
     * @return void
     * 
     * @throws ProxyException
     */
    protected function verifiyData(array $data): void
    {
        $proxyValidKeys = array_keys($this->getProxyInstanceData());
        foreach ($data as $name => $value) {
            if (!in_array($name, $proxyValidKeys)) {
                throw new ProxyException("Invalid property $name");
            }
        }
    }

    /**
     * Hydates the proxy with the component data.
     *
     * @return void
     * 
     * @throws ProxyException
     */
    protected function hydrateProxyData(): void
    {
        if ($this->proxy === null) {
            return;
        }

        $data = $this->getProxyArrayData();
        $this->verifiyData($data);
        $this->setProxyInstanceData($data);
    }

    /**
     * Dehydrates the proxy and set the data in the component.
     *
     * @return void
     */
    protected function dehydrateProxyData(): void
    {
        if ($this->proxy === null) {
            return;
        }

        $data = $this->getProxyInstanceData();
        $this->setProxyArrayData($data);
    }

    /**
     * Makes a new proxy instance.
     *
     * @param string    $name           name of the proxy
     * @param array     $initialData    initial data
     * @return void
     */
    protected function make(string $name, array $initialData = []): void
    {
        /**
         * @var Property|null
         */
        $proxies = Inspector::inspect($this->component)
            ->property()
            ->withName('proxies')
            ->withModifiers(InspectorQuery::PROTECTED_MODIFIER)
            ->first();

        if ($proxies === null) {
            throw new ProxyException("Undefined proxies on component: {$this->component->getName()}");
        }

        /**
         * @var array
         */
        $proxies = $proxies->value();

        if (!in_array($name, array_keys($proxies))) {
            throw new ProxyException("Invalid proxy: $name");
        }

        $proxyClass = $proxies[$name];

        if (!is_subclass_of($proxyClass, Proxy::class)) {
            throw new ProxyException("$name is not a valid proxy");
        }

        $this->proxy = app()->make($proxyClass, ['component' => $this->component, 'name' => $name]);
        $this->setProxyInstanceData($initialData);
    }

    /**
     * Normalizes wire method parameters.
     *
     * @param string|null   $parameters string parameters
     * @param array         $arrays     array with array parameters
     * @return array
     */
    protected function normalizeWireMethodParameters(?string $parameters, array &$arrays): array
    {
        if ($parameters !== null) {
            preg_match_all('/\[(?<array>.[^\[\]]*)?\]/', $parameters, $arrays, PREG_SET_ORDER, 0);

            foreach ($arrays as $index => $array) {
                $parameters = preg_replace('/'.preg_quote($array[0], '/').'/', "ARR::$index", $parameters, 1);
            }

            return explode(',', $parameters);
        }

        return [];
    }

    /**
     * Serializes the wire method parameters.
     *
     * @param array $values     array of values
     * @param array $arrays     array of array values
     * @param mixed $enclose    encloses the result in parenthesis
     * @return string
     */
    protected function serializeWireMethodParameters(array $values, array $arrays, bool $enclose = false): string
    {
        if (!empty($values)) {
            $values = array_map(function($part) use ($arrays) {
                $part = trim($part);

                if (str_starts_with($part, 'ARR::')) {
                    $index = str_replace('ARR::', '', $part);
                    return $arrays[$index][0];
                }

                return $part;
            }, $values);
            $values = implode(',', $values);

            return $enclose ? "($values)" : $values;
        }

        return $enclose ? '()' : '';
    }

    /**
     * Parses the livewire method.
     *
     * @param string        $type           method type
     * @param string|null   $name           method name
     * @param string|null   $parameters     method value
     * @param bool          $parenthesis    method has parenthesis
     * @return string|null
     */
    protected function parseWireMethod(string $type, ?string $name, ?string $parameters, bool $parenthesis): ?string
    {
        $arrays = [];
        $values = $this->normalizeWireMethodParameters($parameters, $arrays);

        switch ($type) {
            case '$wire':
                if (str_starts_with($name, 'parent.')) {
                    $name = str_replace('parent.', '', $name);

                    $method = "$type.$name";
                    $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                    return $method.$values;
                }

                if ($name == 'call') {
                    $name = str_replace('\'', '', $values[0]);
                    unset($values[0]);

                    $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                    return $this->parseWireMethod('', $name, $values, $parenthesis);
                } else if ($name == 'entangle') {
                    $values[0] = str_replace('\'', '', $values[0]);
                    $values[0] = "'proxyData.{$values[0]}'";

                    $method = "$type.$name";
                    $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                    return $method.$values;
                } else {
                    if ($parenthesis) {
                        $method = $this->parseWireMethod($name, $name, $parameters, $parenthesis);

                        if ($method !== null) {
                            return "$type.$method";
                        }
                    }

                    if (str_starts_with($name, 'parent.')) {
                        $name = str_replace('parent.', '', $name);
                    } else {
                        $name = "proxyData.{$name}";
                    }
                    
                    return "$type.$name";
                }

                break;
            case '$get':
            case '$set':
            case '$toggle':
                if (empty($values)) {
                    return null;
                }

                if ($name == 'parent') {
                    $method = "$type";
                    $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                    return $method.$values;
                }

                $values[0] = str_replace('\'', '', $values[0]);
                $values[0] = "'proxyData.{$values[0]}'";
                $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                return $type.$values;
            case '$refresh':
                return null;
                break;
            case '$emitSelf':
                if ($name == 'parent') {
                    $method = $type;
                    $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                    return $method.$values;
                }

                $listener = $values[0];
                unset($values[0]);

                $values = $this->serializeWireMethodParameters($values, $arrays, false);

                $values = [
                    '\'callerCallback\'',
                    '\'proxy\'',
                    "'{$this->proxy->name()}'",
                    $listener,
                    "[$values]",
                ];
                $values = implode(',', $values);

                return "$type($values)";
            default:
                if (str_starts_with($type, '$')) {
                    return null;
                }

                if (str_starts_with($name, 'parent.')) {
                    $method = str_replace('parent.', '', $name);
                    $values = $this->serializeWireMethodParameters($values, $arrays, $parenthesis);

                    return $method.$values;
                }

                $values = $this->serializeWireMethodParameters($values, $arrays, false);

                if (empty($values)) {
                    return "callProxyMethod('$name')";
                }

                return "callProxyMethod('$name', $values)";
        }

        return null;
    }

    /**
     * Parses livewire attribute value.
     *
     * @param string $name     attribute name
     * @param string $value    attribute value
     * @return string|null
     */
    protected function parseWireAttributeValue(string $name, string $value): ?string
    {
        if (str_starts_with($name, 'parent.')) {
            return null;
        }

        if (str_starts_with($name, 'model')) {
            return "proxyData.$value";
        }

        preg_match_all('/(?<name>^[^\$][\w\.\?]+)(?<parenthesis>\((?<parameters>.*)\))?/', $value, $methods, PREG_SET_ORDER, 0);

        foreach ($methods as $method) {
            $name = $method['name'];
            $parenthesis = isset($method['parenthesis']);
            $parameters = $method['parameters'] ?? null;

            $replacement = $this->parseWireMethod('', $name, $parameters, $parenthesis);

            if ($replacement !== null) {
                return $replacement;
            }
        }

        return null;
    }

    /**
     * Parses the proxy view contents.
     *
     * @param View      $view       proxy view
     * @param string    $contents   string with the contents
     * @return string
     */
    protected function parseViewContents(View $view, string $contents): string
    {
        preg_match_all('/wire:(?<name>[\s\n\r]*[\w\.]+)[\s\n\r]*=[\'|"](?<value>.*)[\'|"]/', $contents, $attributes, PREG_SET_ORDER, 0);

        foreach ($attributes as $attribute) {
            $fragment = $attribute[0];

            $name = $attribute['name'];
            $value = $attribute['value'];

            $value = $this->parseWireAttributeValue($name, $value);

            if ($value !== null) {
                $contents = str_replace($fragment, "wire:$name=\"$value\"", $contents);
            }
        }

        preg_match_all('/(?<type>\$\w+)((\.(?<name>[\s\n\r]*[\w\.\?]+)[\s\n\r]*)?(?<parenthesis>\((?<parameters>.*)\))?)?/', $contents, $methods, PREG_SET_ORDER, 0);

        foreach ($methods as $method) {
            $fragment = $method[0];

            $type = $method['type'];
            $name = $method['name'] ?? null;
            $parenthesis = isset($method['parenthesis']);
            $parameters = $method['parameters'] ?? null;

            $replacement = $this->parseWireMethod($type, $name, $parameters, $parenthesis);

            if ($replacement !== null) {
                $contents = str_replace($fragment, $replacement, $contents);
            }
        }

        return $contents;
    }

    /**
     * Changes the proxy.
     *
     * @param string    $proxyName      name of the proxy
     * @param array     $initialData    initial data
     * @return void
     */
    public function change(string $proxyName, array $initialData = []): void
    {
        $this->component->clearValidation();
        
        $this->make($proxyName, $initialData);

        $this->component->proxyData = [];
        $this->component->proxyName = $proxyName;

        if (!$this->proxy->_mounted) {
            $this->proxy->mount();
            $this->proxy->_mounted = true;
        }

        $this->dehydrateProxyData();
    }

    /**
     * @inheritDoc
     */
    public function hydrate(): void
    {
        if ($this->component->proxyName !== null) {
            $this->make($this->component->proxyName);
            $this->hydrateProxyData();
            $this->proxy->hydrate();

            if (!$this->proxy->_mounted) {
                $this->proxy->mount();
                $this->proxy->_mounted = true;
            }

            $this->dehydrateProxyData();
        }
    }

    /**
     * @inheritDoc
     */
    public function dehydrate(): void
    {
        if ($this->proxy !== null) {
            $this->dehydrateProxyData();
            $this->proxy->dehydrate();
        }
    }

    /**
     * @inheritDoc
     */
    public function updating(string $key, mixed $value): void
    {
        if (!str_starts_with($key, 'proxyData.')) {
            return;
        }

        if ($this->proxy !== null) {
            $realKey = str_replace('proxyData.', '', $key);

            $protectedProperties = $this->proxy->getProtectedProperties();
            PropertyProtectionService::throwIfProtected($realKey, $protectedProperties);

            $this->proxy->updating($realKey, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function updated(string $key, mixed $value): void
    {
        if (!str_starts_with($key, 'proxyData.')) {
            return;
        }

        if ($this->proxy !== null) {
            $data = $this->getProxyInstanceData();

            $realKey = str_replace('proxyData.', '', $key);

            if (!Arr::has($data, $realKey)) {
                throw new ProxyException("Invalid key for proxy data: $key");
            }

            data_set($this->proxy, $realKey, $value);
            $this->proxy->updated($realKey, $value);
        }
    }

    /**
     * Gets the current proxy.
     *
     * @return Proxy|null
     */
    public function proxy(): ?Proxy
    {
        return $this->proxy;
    }

    /**
     * Set a property on the proxy.
     *
     * @param string    $key    property name
     * @param mixed     $value  property value
     * @return void
     * 
     * @throws ProxyException
     */
    public function setValue(string $key, mixed $value): void
    {
        if ($this->proxy === null) {
            throw new ProxyException("Proxy not instantiated");
        }

        $this->proxy->$key = $value;
    }

    /**
     * Calls a method on the proxy instance.
     *
     * @param string    $methodName     method name
     * @param mixed     ...$parameters  parameters
     * @return mixed
     * 
     * @throws ProxyException
     */
    public function callProxyMethod(string $methodName, mixed ...$parameters): mixed
    {
        if ($this->proxy === null) {
            throw new ProxyException("Proxy not instantiated");
        }

        $proxyMethods = Inspector::inspect(Proxy::class)
            ->method()
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->all();

        /**
         * @var Method
         */
        foreach ($proxyMethods as $proxyMethod) {
            if ($proxyMethod->name() == $methodName) {
                throw new ProxyException("$methodName is not a callable method");
            }
        }

        /**
         * @var Method|null
         */
        $method = Inspector::inspect($this->proxy)
            ->method()
            ->withName($methodName)
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->first();

        if ($method === null) {
            throw new ProxyException("Undefined or not public method named $methodName");
        }

        $result = $method->invoke(...$parameters);

        $this->dehydrateProxyData();
        
        return $result;
    }

    /**
     * Handles the proxy callback event.
     *
     * @param string    $name       proxy name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * @return mixed
     * 
     * @throws ProxyException
     */
    public function handleProxyCallbackEvent(string $name, string $event, array $parameters): mixed
    {
        if ($this->proxy === null) {
            return null;
        }

        if ($this->proxy->name() != $name) {
            throw new ProxyException("Proxy mismatch: $name");
        }

        return $this->proxy->onEvent($event, $parameters) ?? null;
    }

    /**
     * Render the proxy.
     *
     * @param View $view component view
     * @return string|null
     */
    public function render(View $view): ?View
    {
        $proxyView = $this->proxy?->render();

        if ($proxyView !== null) {
            $componentErrorBag = $this->component->getErrorBag();
            $proxyErrorBag = new MessageBag();

            foreach ($componentErrorBag->messages() as $key => $messages) {
                if (!str_starts_with($key, 'proxyData.')) {
                    continue;
                }

                foreach ($messages as $message) {
                    $proxyErrorBag->add(str_replace('proxyData.', '', $key), $message);
                }
            }

            $proxyView = $proxyView->with($this->getProxyInstanceData())
                ->withErrors($proxyErrorBag)
                ->render(function ($view, $contents) {
                    return $this->parseViewContents($view, $contents);
                });

            $view->with('proxyView', $proxyView);
        } else {
            $view->with('proxyView', '');
        }

        return $view;
    }
}
