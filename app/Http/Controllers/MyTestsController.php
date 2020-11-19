<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Http\Requests\TestRequestFilter;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Question;
use App\Models\QuestionStudent;
use App\Models\Test;
use App\Models\TestAssistant;
use App\Models\TestStudent;
use App\Models\User;
use App\Parents\FrontEndController;
use App\Services\SidebarService;
use App\Tables\MyTestsTable;
use App\Tables\TestsTable;
use App\Tables\TestStudentsTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class MyTestsController
 *
 * @package App\Http\Controllers
 */
final class MyTestsController extends FrontEndController
{
    /**
     * @var string|null $activeItem
     */
    protected ?string $activeItem = SidebarService::ITEM_MY_TESTS;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Tables\MyTestsTable $table
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(MyTestsTable $table): \Illuminate\Contracts\View\View
    {
        $this->setTitle('pages.tests.index');
        return $this->view('app.tests.my.index', compact('table'));
    }

    /**
     * Get data in json response and apply filters set in request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Tables\MyTestsTable $table
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexJson(Request $request, MyTestsTable $table): \Illuminate\Http\JsonResponse
    {
        $this->authService->user();
        return $this->responseFactory->json($table->getData($request));
    }

    /**
     * @param \App\Tables\MyTestsTable $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonFilters(MyTestsTable $table): JsonResponse
    {
        return $this->responseFactory->json($table->getFilters());
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
