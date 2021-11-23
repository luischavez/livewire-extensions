<?php

namespace Luischavez\Livewire\Extensions\Widgets;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Luischavez\Livewire\Extensions\ProxyComponent;
use Luischavez\Livewire\Extensions\Traits\WithAuth;

/**
 * Auth system.
 */
class AuthSystem extends ProxyComponent
{
    use WithAuth;

    /**
     * Pages.
     *
     * @var array
     */
    protected array $proxies = [];

    /**
     * HTML id of the parent content.
     * 
     * @var string
     */
    public ?string $parentId = null;

    /**
     * Show the modal.
     * 
     * @var bool
     */
    public bool $show = false;

    /**
     * Display the component as a modal.
     * 
     * @var bool
     */
    public bool $modal = true;

    /**
     * Auth timeout.
     * 
     * @var int
     */
    public int $timeout = 0;

    /**
     * Redirect after seconds.
     * 
     * @var int
     */
    public int $redirectAfter = 0;

    /**
     * Redirect fallback route name.
     * 
     * @var string
     */
    public string $redirectFallback = '';

    /**
     * Redirect to the previous page.
     * 
     * @var bool
     */
    public bool $redirecting = false;

    /**
     * Redirect to route on logout.
     *
     * @var string|null
     */
    public ?string $redirectOnLogout = null;

    /**
     * Protected properties.
     * 
     * @var array
     */
    protected array $protectedProperties = [
        '*',
        '_except' => [
            'proxyData',
            'redirectAfter',
            'show',
            'modal',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->proxies = config('livewire-ext.auth.pages');
    }

    /**
     * Mount the component.
     *
     * @param string $page page
     * @return void
     */
    public function mount(?string $page = null): void
    {
        $this->modal = $page === null;
        
        if ($page !== null) {
            $this->changePage($page);
        }

        $this->timeout = config('livewire-ext.auth.timeout');
        $this->redirectAfter = config('livewire-ext.auth.redirect_after');
        $this->redirectFallback = config('livewire-ext.auth.redirect_fallback');

        $this->redirecting = false;
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        /**
         * @var View
         */
        $view = view('livewire-ext::widgets.auth-system');

        if (!$this->modal) {
            $view->layout(config('livewire-ext.auth.layout'));
        }

        return $view;
    }

    /**
     * Redirect to the previous page.
     *
     * @return void
     */
    public function redirectBack(): void
    {
        $actionRoute = route('auth.system.page', $this->proxyName);
        $fallback = route($this->redirectFallback);
        $previous = url()->previous($fallback);

        if ($previous == $actionRoute) {
            $previous = $fallback;
        }

        $this->redirect($previous);
    }

    /**
     * Check if the user session needs to be expired.
     *
     * @return void
     */
    public function check(): void
    {
        if ($this->timeout <= 0) return;

        /**
         * @var Carbon
         */
        $userLastActivity = session()->get('user_last_activity');

        if (!$userLastActivity) return;

        $now = now();

        $elapsedMinutes = $userLastActivity->diffInMinutes($now);
        
        if ($elapsedMinutes >= $this->timeout) {
            // TODO: Trigger session expired event.
            $this->authService->logout();
        }
    }

    /**
     * Changes the page.
     *
     * @param string $page page
     * @return void
     */
    public function changePage(string $page): void
    {
        $this->changeProxy($page);
        $this->show = true;
    }

    /**
     * Closes the page.
     *
     * @return void
     */
    public function close(): void
    {
        $this->clearValidation();

        $this->show = false;

        if (!$this->modal) {
            $this->redirecting = true;
        }
    }

    /**
     * Executes the auth page.
     *
     * @return void
     */
    public function execute(): void
    {
        if ($this->callProxyMethod('execute')) {
            $this->close();
        }
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'widgets-auth-system';
    }

    /**
     * Auth routes.
     *
     * @return void
     */
    public static function routes(): void
    {
        $pages = config('livewire-ext.auth.pages');
        $pageNames = array_keys($pages);

        Route::get('/auth/{page}', AuthSystem::class)
            ->where('page', implode('|', $pageNames))
            ->name('auth.system.page');
    }
}
