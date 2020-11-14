<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\GroupsRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Http\Requests\GroupRequestFilter;
use App\Models\Group;
use App\Parents\FrontEndController;
use App\Services\GroupService;
use Illuminate\Http\Request;

/**
 * Class GroupController
 *
 * @package App\Http\Controllers
 */
final class GroupController extends FrontEndController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\GroupRequestFilter $filter
     * @param \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     * @param \App\Services\GroupService $groupService
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(
        Request $request,
        GroupRequestFilter $filter,
        GroupsRepositoryInterface $groupsRepository,
        GroupService $groupService
    ) {
        $this->authorize(PermissionsEnum::CREATE, Group::class);
        $group = $groupsRepository->create($filter->validated($request));
        if ($filter->getQuestions() !== null) {
            $groupService->saveQuestions($group, $filter->getQuestions());
        }

        $group->load(Group::RELATION_QUESTIONS);

        if ($request->wantsJson()) {
            return $this->responseFactory->json($group->toArray(), 201);
        }
        return $this->back();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\GroupRequestFilter $filter
     * @param \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     * @param \App\Services\GroupService $groupService
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(
        Request $request,
        GroupRequestFilter $filter,
        GroupsRepositoryInterface $groupsRepository,
        GroupService $groupService,
        int $id
    ) {
        $group = $groupsRepository->get($id);
        $this->authorize(PermissionsEnum::EDIT, $group);
        $group->compactFill($filter->validated($request));
        $groupsRepository->save($group);

        if ($filter->getQuestions() !== null) {
            $groupService->saveQuestions($group, $filter->getQuestions());
        }

        $group->load(Group::RELATION_QUESTIONS);

        if ($request->wantsJson()) {
            return $this->responseFactory->json($group->toArray(), 200);
        }
        return $this->back();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Request $request, GroupsRepositoryInterface $groupsRepository, int $id)
    {
        $group = $groupsRepository->get($id);
        $this->authorize(PermissionsEnum::DELETE, $group);
        $groupsRepository->delete($group);

        if ($request->wantsJson()) {
            return $this->responseFactory->noContent();
        }

        return $this->back();
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
