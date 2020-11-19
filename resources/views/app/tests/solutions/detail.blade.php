@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Models\GroupStudent $groupSolution
     * @var \Illuminate\Support\Collection|\App\Models\QuestionStudent[] $questionSolutions
     */
    $group = $groupSolution->group;
    $test = $group->test;
@endphp

@extends('layouts.app')

@section('content')
    <group-evaluation :question-solutions="{{ $questionSolutions->toJson() }}" inline-template>
        <div>
            <div class="row mb-4">
                <div class="table-responsive col-12 col-lg-6">
                    <table class="table table-hover test-info-table">
                        <tbody>
                        <tr>
                            <th scope="row" width="200">{{ __('labels.group') }}</th>
                            <td>{{ $group->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" width="200">{{ __('labels.max_points') }}</th>
                            <td>{{ $questionSolutions->sum(static function (\App\Models\QuestionStudent $questionStudent): int {
                                return $questionStudent->question->max_points;
                            }) }}</td>
                        </tr>
                        <tr>
                            <th scope="row" width="200">{{ __('labels.points') }}</th>
                            <td>@{{ pointsTotal }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <form action="{{ route('tests.evaluate', $test->id) }}" method="POST">
                @csrf
                <div id="accQuestions">
                    <div v-for="(solution, index) in dataSolutions">
                        <input type="hidden" :name="`solutions[${index}][id]`" :value="solution.id">
                        <div class="card mb-2" v-for="question in [solution.question]">
                            <a :href="`#collapse-${question.id}`" data-toggle="collapse" role="button"
                               class="text-primary"
                               aria-expanded="false"
                               :aria-controls="`collapse-${question.id}`">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                     :id="`heading-${question.id}`">
                                    <h5 class="h5 mb-0" style="min-width: 300px;">
                                        @{{ index + 1 }}. Otázka
                                    </h5>
                                    <div>
                                        <span>@{{ solution.points }} / @{{ question.max_points }}</span>
                                    </div>
                                </div>
                            </a>

                            <div :id="`collapse-${question.id}`" class="collapse"
                                 :aria-labelledby="`heading-${question.id}`"
                                 data-parent="#accQuestions">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h4 class="h4">@{{ question.name }}</h4>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <p>@{{ question.text }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div v-for="file in question.files" class="col-12">
                                            <a :href="file.file_url" class="text-primary"
                                               target="_blank">
                                                <img v-if="file.mime_type.indexOf('image') === 0"
                                                     :src="file.file_url"
                                                     :alt="file.name" width="360">
                                                <span v-else>
                                                @{{ file.name }}
                                            </span>
                                            </a>
                                        </div>
                                    </div>

                                    <div
                                        v-if="question.type === {{ \App\Enums\QuestionTypesEnum::OPTIONS }} || question.type === {{ \App\Enums\QuestionTypesEnum::OPTIONS_CHECKBOX }}"
                                        class="row">
                                        <div class="col-12">
                                            <label v-if="question.type === 0">
                                                Vyberte jednu z možností
                                            </label>
                                            <label v-else>
                                                Vyberte možnosti
                                            </label>

                                            <ul class="list-group">
                                                <li v-for="option in question.options"
                                                    :class="`list-group-item ${getOptionClass(index, option.id)} d-flex justify-content-between align-items-center`">
                                                    <span>@{{ option.text }}</span>
                                                    <span>Body: @{{ option.points }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div v-else-if="question.type === {{ \App\Enums\QuestionTypesEnum::LINE }}"
                                         class="form-group">
                                        <label>Odpověď</label>
                                        <p>@{{ solution.text }}</p>
                                    </div>

                                    <div v-else-if="question.type === {{ \App\Enums\QuestionTypesEnum::TEXT }}"
                                         class="form-group">
                                        <label>Odpověď</label>
                                        <p>@{{ solution.text }}</p>
                                    </div>

                                    <div v-else="question.type === {{ \App\Enums\QuestionTypesEnum::TEXT }}"
                                         class="form-group">
                                        <label>Soubory</label>
                                        <div class="row">
                                            <div v-for="file in solution.files" class="col-12">
                                                <a :href="file.file_url" class="text-primary"
                                                   target="_blank">
                                                    <img v-if="file.mime_type.indexOf('image') === 0"
                                                         :src="file.file_url"
                                                         :alt="file.name" width="360">
                                                    <span v-else>
                                                @{{ file.name }}
                                            </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{--            Evaluation                    --}}
                                    <div class="form-group mt-3">
                                        <label :for="`solutions[${index}][points]`">
                                            {{ __('labels.points') }} (Min: @{{ question.min_points }}, Max: @{{
                                            question.max_points }})
                                        </label>
                                        <input type="number" class="form-control" :min="question.min_points"
                                               :max="question.max_points"
                                               v-model="solution.points" @input="$forceUpdate()"
                                               :id="`solutions[${index}][points]`"
                                               :name="`solutions[${index}][points]`"
                                               onkeydown="return event.key !== 'Enter';">
                                    </div>
                                    <div class="form-group mt-3">
                                        <label :for="`solutions[${index}][notes]`">
                                            Poznámka k hodnocení
                                        </label>
                                        <textarea class="form-control" max="10000" v-model="solution.notes"
                                                  :id="`solutions[${index}][notes]`"
                                                  :name="`solutions[${index}][notes]`"
                                                  @change="$forceUpdate()">
                                    </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Potvrdit hodnocení</button>
            </form>
        </div>
    </group-evaluation>
@endsection
