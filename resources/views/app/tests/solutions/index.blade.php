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
    <div class="row mb-4">
        <div class="table-responsive col-12 col-lg-6">
            <table class="table table-hover test-info-table">
                <tbody>
                <tr>
                    <th scope="row" width="200">{{ __('labels.end') }}</th>
                    <td>{{ $test->start_date->addMinutes($test->time_limit)->format('d. m. Y H:i:s') }}</td>
                </tr>

                <tr>
                    <th scope="row" width="200">{{ __('labels.max_points') }}</th>
                    <td>{{ $questionSolutions->sum(static function (\App\Models\QuestionStudent $questionStudent): int {
                        return $questionStudent->question->max_points;
                    }) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <group-solution :group-solution="{{ $groupSolution->toJson() }}" inline-template>
                <div>
                    <div class="mb-3">
                        <button class="btn btn-primary" @click="finish">{{ __('labels.finish') }}</button>
                    </div>

                    <div id="accQuestions">
                        @foreach($questionSolutions as $index => $questionSolution)
                            <question-solution :question-solution="{{ $questionSolution->toJson() }}" inline-template ref="`questions[{{ $index }}]`">
                                <div>
                                    <div class="card mb-2">
                                        <div :class="`card-header d-flex justify-content-between align-items-center ${saved ? 'bg-success' : ''}`"
                                             id="heading-${question.id}`" :title="saved ? 'Uloženo' : 'Neuloženo' ">
                                            <a :href="`#collapse-${question.id}`" data-toggle="collapse" role="button"
                                               class="text-primary"
                                               aria-expanded="false"
                                               :aria-controls="`collapse-${question.id}`">
                                                <h5 class="h5 mb-0" style="min-width: 300px;">
                                                    {{ $loop->index + 1 }}. Otázka
                                                </h5>
                                            </a>
                                            <div>
                                                <span class="cursor-pointer mr-4"
                                                      title="{{ __('labels.update') }}"
                                                      @click="save">
                                                    <i class="fas fa-save fa-2x text-primary align-middle"></i>
                                                </span>
                                                <span>Min @{{ question.min_points }} / Max @{{ question.max_points }}</span>
                                            </div>
                                        </div>

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

                                                <div v-if="question.type === {{ \App\Enums\QuestionTypesEnum::OPTIONS }} || question.type === {{ \App\Enums\QuestionTypesEnum::OPTIONS_CHECKBOX }}"
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
                                                                @click="clickOption(option.id)"
                                                                :class="`list-group-item ${selectedOptions[option.id] ? 'list-group-item-success' : ''} d-flex justify-content-between align-items-center`">
                                                                <span>@{{ option.text }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div v-else-if="question.type === {{ \App\Enums\QuestionTypesEnum::LINE }}" class="form-group">
                                                    <label>Zadejte odpověď</label>
                                                    <input type="text" class="form-control" maxlength="1000" v-model="dataSolution.text" @input="saved = false">
                                                </div>

                                                <div v-else-if="question.type === {{ \App\Enums\QuestionTypesEnum::TEXT }}" class="form-group">
                                                    <label>Zadejte odpověď</label>
                                                    <textarea class="form-control" maxlength="10000" v-model="dataSolution.text" @input="saved = false">
                                                    </textarea>
                                                </div>

                                                <div v-else="question.type === {{ \App\Enums\QuestionTypesEnum::TEXT }}" class="form-group">
                                                    <label>Nahrejte soubory (Max: @{{ question.files_number }})</label>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <ul class="list-group">
                                                                <li v-for="(file, fileIndex) in dataSolution.files"
                                                                    :key="`files-${fileIndex}`"
                                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <span v-bind:class="{'text-danger': hasFileError(fileIndex)}">
                                                                        @{{ file.name }} (@{{ (file.size / 1000).toFixed(1) }} KB)
                                                                    </span>
                                                                    <span>
                                                                    <i class="fas fa-times text-danger cursor-pointer"
                                                                       @click="removeFile(fileIndex)">
                                                                    </i>
                                                                </span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mt-1">
                                                        <button class="btn btn-light"
                                                                @click="(event) => event.target.parentElement.querySelector('input').click()">
                                                            Nahrát soubory
                                                        </button>
                                                        (Max 2 MB / soubor)
                                                        <input type="file" class="form-control-file d-none h-0" :id="`question-files-${question.id}`"
                                                               multiple="multiple" @change="(event) => uploadFiles(event)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </question-solution>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary" @click="finish">{{ __('labels.finish') }}</button>
                    </div>
            </group-solution>
        </div>
    </div>
@endsection
