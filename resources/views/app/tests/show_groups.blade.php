@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Models\Test $test
     */
@endphp

<div>
    <div id="groupLabels">
        @foreach($test->groups as $group)
            @php $active = $loop->first; @endphp
            @component('app.components.panel-label', ['active' => $active, 'target' => 'panelGroup-' . $group->id, 'color' => 'success', 'parent' => 'groupLabels'])
                @slot('label')
                    {{ $group->name }}
                @endslot
            @endcomponent
        @endforeach
    </div>

    <div id="groupPanels">
        @foreach($test->groups as $group)
            @component('app.components.form-panel', ['id' => 'panelGroup-' . $group->id, 'active' => $loop->first ? true : false, 'parent' => 'groupPanels', 'color' => 'success'])
                <div id="accQuestions">
                    @foreach($group->questions as $question)
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
                                    <span>Min {{ $question->min_points }} / Max {{ $question->max_points }}</span>
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
                                            @if ($question->type === \App\Enums\QuestionTypesEnum::OPTIONS)
                                                Vyberte jednu z možností
                                            @else
                                                Vyberte možnosti
                                            @endif
                                            </label>

                                            <ul class="list-group">
                                                @foreach($question->options as $option)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $option->text }}</span>
                                                        <span>Body: {{ $option->points }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endcomponent
        @endforeach
    </div>
</div>
