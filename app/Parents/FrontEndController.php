<?php declare(strict_types=1);

namespace App\Parents;

use App\Services\AuthService;
use App\Services\SidebarService;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory;
use Mckenziearts\Notify\LaravelNotify;

/**
 * Class FrontEndController
 *
 * @package App\Parents
 */
abstract class FrontEndController extends Controller
{
    /**
     * Title of page
     *
     * @var string|null $title
     */
    protected ?string $title = null;

    /**
     * @var string|null $activeItem
     */
    protected ?string $activeItem = null;

    /**
     * @var \Illuminate\Contracts\View\Factory $viewFactory
     */
    protected Factory $viewFactory;

    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     */
    protected ResponseFactory $responseFactory;

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    protected UrlGenerator $urlGenerator;

    /**
     * @var \Illuminate\Contracts\Translation\Translator $translator
     */
    protected Translator $translator;

    /**
     * @var \Mckenziearts\Notify\LaravelNotify $notify
     */
    protected LaravelNotify $notify;

    /**
     * @var \Illuminate\Contracts\Config\Repository $config
     */
    protected Repository $config;

    /**
     * @var \App\Services\AuthService $authService
     */
    protected AuthService $authService;

    /**
     * @var \App\Services\SidebarService $sidebarService
     */
    protected SidebarService $sidebarService;

    /**
     * FrontEndController constructor.
     *
     * @param \Illuminate\Contracts\View\Factory $viewFactory
     * @param \Illuminate\Contracts\Routing\ResponseFactory $responseFactory
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param \Illuminate\Contracts\Translation\Translator $translator
     * @param \Mckenziearts\Notify\LaravelNotify $notify
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \App\Services\AuthService $authService
     * @param \App\Services\SidebarService $sidebarService
     */
    public function __construct(
        Factory $viewFactory,
        ResponseFactory $responseFactory,
        UrlGenerator $urlGenerator,
        Translator $translator,
        LaravelNotify $notify,
        Repository $config,
        AuthService $authService,
        SidebarService $sidebarService
    ) {
        $this->viewFactory = $viewFactory;
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->notify = $notify;
        $this->config = $config;
        $this->authService = $authService;
        $this->sidebarService = $sidebarService;

        $this->initMiddleware();
    }

    /**
     * Set title of page
     *
     * @param string $title
     * @param array|string[] $replace
     * @return void
     */
    protected function setTitle(string $title, array $replace = []): void
    {
        $this->title = $this->translator->get($title, $replace);
    }

    /**
     * @param string|null $activeItem
     */
    public function setActiveItem(?string $activeItem): void
    {
        $this->activeItem = $activeItem;
    }

    /**
     * Redirect to previous url
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function back(): \Illuminate\Http\RedirectResponse
    {
        return $this->responseFactory->redirectTo($this->urlGenerator->previous());
    }

    /**
     * Return view with default data
     *
     * @param string $view
     * @param mixed[] $data
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function view(string $view, array $data = []): \Illuminate\Contracts\View\View
    {
        return $this->viewFactory->make($view, $data, $this->getDefaultData());
    }

    /**
     * Initialize middleware
     *
     * @return void
     */
    protected function initMiddleware(): void
    {
    }

    /**
     * Get default view data
     *
     * @return mixed[]
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function getDefaultData(): array
    {
        return [
            'title' => $this->title,
            'auth' => $this->authService->user(),
            'sidebar' => $this->sidebarService->getSidebar($this->activeItem),
        ];
    }
}
