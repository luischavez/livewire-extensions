<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Services\TaggingService;

/**
 * Enables tag.
 */
trait WithTag
{
    /**
     * Tagging service.
     *
     * @var TaggingService
     */
    protected TaggingService $taggingService;

    /**
     * Component tag.
     *
     * @var string
     */
    public string $tag = '';

    /**
     * Protected properties.
     *
     * @return array
     */
    protected function protectPropertiesWithTag(): array
    {
        return ['tag'];
    }

    /**
     * Emit an event.
     *
     * @param string        $event
     * @param string|null   $tag
     * @param string|null   $component
     * @param mixed         ...$parameters
     * @return void
     */
    protected function emitEvent(string $event, ?string $tag = null, ?string $component = null, mixed ...$parameters): void
    {
        $this->taggingService->emitEvent($event, $tag, $component, ...$parameters);
    }

    /**
     * Gets the component tag.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }
}
