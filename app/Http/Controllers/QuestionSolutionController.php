<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Http\Requests\QuestionSolutionRequestFilter;
use App\Http\Requests\StartTestRequestFilter;
use App\Http\Requests\TestSolutionRequestFilter;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionStudent;
use App\Parents\FrontEndController;
use App\Queries\QuestionStudentQueryBuilder;
use App\Services\QuestionsService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Class QuestionSolutionController
 *
 * @package App\Http\Controllers
 */
final class QuestionSolutionController extends FrontEndController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\QuestionSolutionRequestFilter $filter
     * @param \App\Services\QuestionsService $questionsService
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(
        Request $request,
        QuestionSolutionRequestFilter $filter,
        QuestionsService $questionsService,
        int $id
    ) {
        /** @var QuestionStudent $solution */
        $solution = $this->query()->getById($id);
        $this->authorize(PermissionsEnum::EDIT, $solution);
        $solution->compactFill($filter->validated($request, $solution));
        $solution->save();

        $options = $filter->getOptions();
        if ($options !== null) {
            $solution->options()->sync($options);
            $solution->compactFill([
                QuestionStudent::ATTR_POINTS => $solution->options->sum(static function (Option $option): int {
                    return $option->points;
                }),
            ]);
            $solution->save();
        }

        $filesData = $filter->getFiles();
        if ($filesData !== null) {
            $questionsService->saveSolutionFiles($solution, $filesData);
        }

        $solution->load([
            QuestionStudent::RELATION_OPTIONS,
            QuestionStudent::RELATION_QUESTION . '.' . Question::RELATION_GROUP,
            QuestionStudent::RELATION_FILES
        ]);

        if ($request->wantsJson()) {
            if ($this->authService->user()->role < RolesEnum::ROLE_ASSISTANT) {
                $solution->makeHidden(QuestionStudent::ATTR_POINTS);
            }

            return $this->responseFactory->json($solution);
        }

        $this->notify->success('Hodnocení uloženo', '');
        return $this->back();
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }

    /**
     * @return \App\Queries\QuestionStudentQueryBuilder
     */
    private function query(): QuestionStudentQueryBuilder
    {
        return new QuestionStudentQueryBuilder(new QuestionStudent());
    }
}
