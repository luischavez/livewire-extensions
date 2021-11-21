<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Luischavez\Livewire\Extensions\Builders\AlertBuilder;
use Luischavez\Livewire\Extensions\Builders\DialogBuilder;
use Luischavez\Livewire\Extensions\Exceptions\ProxyException;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Services\AlertService;
use Luischavez\Livewire\Extensions\Services\DialogService;
use Luischavez\Livewire\Extensions\Services\ProxyService;

/**
 * Component proxy.
 */
abstract class Proxy extends Caller
{
    /**
     * Component.
     *
     * @var Component
     */
    protected Component $component;
    
    /**
     * Name.
     *
     * @var string
     */
    protected string $proxyName;

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [];

    /**
     * Validation rules.
     *
     * @var array
     */
    public array $rules = [];

    /**
     * Validation messages.
     *
     * @var array
     */
    public array $validationMessages = [];

    /**
     * Validation attributes.
     * 
     * @var array
     */
    public array $validationAttributes = [];

    /**
     * Mounted status.
     *
     * @var boolean
     */
    public bool $_mounted = false;

    /**
     * Constructor.
     *
     * @param Component $component  component
     * @param string    $name       proxy
     */
    public function __construct(Component $component, string $name)
    {
        $this->component = $component;
        $this->proxyName = $name;
    }

    /**
     * @inheritDoc
     */
    public function component(): Component
    {
        return $this->component;
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return 'proxy';
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->proxyName;
    }

    /**
     * Gets the protected properties.
     *
     * @return array
     */
    public function getProtectedProperties(): array
    {
        return array_merge($this->protectedProperties, [
            '_mounted',
            'rules',
            'validationMessages',
            'validationAttributes',
        ]);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return $this->rules;
    }

    /**
     * Gets the validation messages.
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    /**
     * Gets the validation attributes.
     *
     * @return array
     */
    protected function getValidationAttributes(): array
    {
        return $this->validationAttributes;
    }

    /**
     * Create a new alert.
     *
     * @param string $title     alert title
     * @param string $message   alert message
     * @return AlertBuilder
     */
    protected function alert(string $title, string $message): AlertBuilder
    {
        /**
         * @var AlertService
         */
        $alertService = AlertService::of($this->component);
        return $alertService->alert($title, $message);
    }

    /**
     * Create a new dialog.
     *
     * @param string $title     dialog title
     * @param string $message   dialog message
     * @return DialogBuilder
     */
    protected function dialog(string $title, string $message): DialogBuilder
    {
        /**
         * @var DialogService
         */
        $dialogService = DialogService::of($this->component);
        return $dialogService->dialog($title, $message);
    }

    /**
     * Adds the proxyData prefix to all array keys.
     *
     * @param array $data data
     * @return array
     */
    protected function prefixArrayWithProxyData(array $data): array
    {
        foreach ($data as $name => $value) {
            if (str_starts_with($name, 'proxyData')) {
                continue;
            }

            $data["proxyData.$name"] = $value;
            unset($data[$name]);
        }

        return $data;
    }

    /**
     * Clears all the validation messages.
     *
     * @return void
     */
    public function clearValidation(): void
    {
        $errorBag = $this->component->getErrorBag();

        foreach ($errorBag->messages() as $key => $messages) {
            if (!str_starts_with($key, 'proxyData.')) {
                continue;
            }

            $this->component->resetErrorBag($key);
        }
    }

    /**
     * Clear a validation field from the error bag.
     *
     * @param string $field field to clear
     * @return void
     */
    public function resetErrorBag(string $field): void
    {
        $this->component->resetErrorBag($field);
    }

    /**
     * Validates all the data with the given rules.
     *
     * @param array $rules      rules
     * @param array $messages   messages
     * @param array $attributes attributes
     * @return array
     */
    public function validate(array $rules = [], array $messages = [], array $attributes = []): array
    {
        if (empty($rules)) {
            $rules = $this->getValidationRules();
        }

        if (empty($messages)) {
            $messages = $this->getValidationMessages();
        }

        if (empty($attributes)) {
            $attributes = $this->getValidationAttributes();
        }

        $rules = $this->prefixArrayWithProxyData($rules);
        $messages = $this->prefixArrayWithProxyData($messages);
        $attributes = $this->prefixArrayWithProxyData($attributes);

        $proxyService = ProxyService::of($this->component);
        $proxyService->dehydrate();

        return $this->component->validate($rules, $messages, $attributes);
    }

    /**
     * Validates only a field with the given rules.
     *
     * @param string    $field      field name
     * @param array     $rules      rules
     * @param array     $messages   messages
     * @param array     $attributes attributes
     * @return array
     */
    public function validateOnly(string $field, array $rules = [], array $messages = [], array $attributes = []): array
    {
        if (empty($rules)) {
            $rules = $this->getValidationRules();
        }

        if (empty($messages)) {
            $messages = $this->getValidationMessages();
        }

        if (empty($attributes)) {
            $attributes = $this->getValidationAttributes();
        }

        $rules = $this->prefixArrayWithProxyData($rules);
        $messages = $this->prefixArrayWithProxyData($messages);
        $attributes = $this->prefixArrayWithProxyData($attributes);

        $proxyService = ProxyService::of($this->component);
        $proxyService->dehydrate();

        return $this->component->validateOnly("proxyData.$field", $rules, $messages, $attributes);
    }

    /**
     * Handles events on this action.
     *
     * @param string    $event
     * @param array     $parameters
     */
    public function onEvent(string $event, array $parameters)
    {
        $methodName = 'on'.Str::studly($event);

        $method = Inspector::inspect($this)
            ->method()
            ->withName($methodName)
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->first();

        if ($method === null) {
            throw new ProxyException("Method $methodName not found or is not public");
        }

        return $this->$methodName(...$parameters) ?? null;
    }

    /**
     * Run on mount.
     *
     * @return void
     */
    public function mount(): void
    {
        
    }

    /**
     * Run on hydate.
     *
     * @return void
     */
    public function hydrate(): void
    {
        
    }

    /**
     * Run on dehydrate.
     *
     * @return void
     */
    public function dehydrate(): void
    {
        
    }

    /**
     * Run on updating.
     *
     * @param string    $key    property name
     * @param     $value  property value
     * @return void
     */
    public function updating(string $key, $value): void
    {

    }

    /**
     * Run on updated.
     *
     * @param string    $key    property name
     * @param     $value  property value
     * @return void
     */
    public function updated(string $key, $value): void
    {
        
    }

    /**
     * Render the proxy.
     *
     * @return View|null
     */
    public function render(): ?View
    {
        return null;
    }
}
