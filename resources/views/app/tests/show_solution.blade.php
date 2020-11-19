@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Models\Test $test
     * @var \App\Models\User[]|\Illuminate\Support\Collection $assistants
     * @var \App\Models\User[]|\Illuminate\Support\Collection $askedAssistants
     * @var \App\Models\GroupStudent $solution
     * @var \Illuminate\Support\Collection|\App\Models\QuestionStudent[] $questionSolutions
    */
@endphp

<div>
    <div class="row mb-4">
        <div class="table-responsive col-12 col-lg-6">
            <table class="table table-hover test-info-table">
                <tbody>
                <tr>
                    <th scope="row" width="200">{{ __('labels.points') }}</th>
                    <td>{{ $questionSolutions->sum(static function (\App\Models\QuestionStudent $questionStudent): int {
                        return $questionStudent->points;
                    }) }} / {{ $questionSolutions->sum(static function (\App\Models\QuestionStudent $questionStudent): int {
                        return $questionStudent->question->max_points;
                    }) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="accQuestions">
        @foreach($questionSolutions as $questionSolution)
            @php $question = $questionSolution->question @endphp
            <div class="card mb-2">
                <a href="#collapse-{{ $question->id }}" data-toggle="collapse" role="button"
                   class="text-primary"
                   aria-expanded="false"
                   aria-controls="collapse-{{ $question->id }}">
                    <div class="card-header  d-flex justify-content-between align-items-center"
                         id="heading-{{ $question->id }}">
                        <h5 class="h5 mb-0">
                            {{ $loop->index + 1 }}. Otázka
                        </h5>
                        <span>{{ $questionSolution->points }} / {{ $question->max_points }}</span>
                    </div>
                </a>

                <div id="collapse-{{ $question->id }}" class="collapse"
                     aria-labelledby="heading-{{ $question->id }}"
                     data-parent="#accQuestions">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="h4">{{ $question->name }}</h4>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p>{{ $question->text }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            @foreach($question->files as $file)
                                <div class="col-12">
                                    <a href="{{ $file->file_url }}" class="text-primary" target="_blank">
                                        @if(\strpos($file->mime_type, "image") === 0)
                                            <img src="{{ $file->file_url }}" alt="{{ $file->name }}" width="360">
                                        @else
                                            {{ $file->name }}
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        @if ($question->type === \App\Enums\QuestionTypesEnum::OPTIONS || $question->type === \App\Enums\QuestionTypesEnum::OPTIONS_CHECKBOX)
                            <div class="row">
                                <div class="col-12">
                                    <label>
                                        Možnosti
                                    </label>

                                    <ul class="list-group">
                                        @foreach($question->options as $option)
                                            @php
                                                if ($questionSolution->options->contains($option->id)) {
                                                    if ($option->points < 0) {
                                                        $class = 'list-group-item-danger';
                                                    } else if ($option->points > 0) {
                                                        $class = 'list-group-item-success';
                                                    } else {
                                                        $class = 'list-group-item-warning';
                                                    }
                                                } else {
                                                    $class = '';
                                                }
                                            @endphp
                                            <li
                                                class="list-group-item {{ $class }} d-flex justify-content-between align-items-center">
                                                <span>{{ $option->text }}</span>
                                                <span>Body: {{ $option->points }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        @elseif ($question->type === \App\Enums\QuestionTypesEnum::LINE || $question->type === \App\Enums\QuestionTypesEnum::TEXT)
                            <div class="row">
                                <div class="col-12">
                                    <label>Odpověď</label>
                                    <p class="mb-2">
                                        {{ $questionSolution->text }}
                                    </p>
                                </div>
                            </div>

                        @elseif ($question->type === \App\Enums\QuestionTypesEnum::FILES)
                            <div class="row">
                                <div class="col-12">
                                    <label>Nahrané soubory</label>
                                    <div class="mb-2">
                                        @foreach($questionSolution->files as $file)
                                            <div class="col-12">
                                                <a href="{{ $file->file_url }}" class="text-primary" target="_blank">
                                                    @if(\strpos($file->mime_type, "image") === 0)
                                                        <img src="{{ $file->file_url }}" alt="{{ $file->name }}" width="360">
                                                    @else
                                                        {{ $file->name }}
                                                    @endif
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
