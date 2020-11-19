@php
/**
 * @var \App\Models\User $auth
 * @var \App\Models\Test $test
 * @var \App\Models\User[]|\Illuminate\Support\Collection $assistants
 * @var \App\Models\User[]|\Illuminate\Support\Collection $askedAssistants
 * @var \App\Models\GroupStudent $solution
 * @var \Illuminate\Support\Collection|\App\Models\QuestionStudent[] $questionSolutions
 * @var \App\Tables\TestStudentsTable $studentsTable
*/
@endphp

@extends('layouts.app')

@section('content')
    @if ($test->isValid())
        <div class="mb-2">
            @can(\App\Enums\PermissionsEnum::REQUEST_ASSISTANT, $test)
                <form action="{{ route('tests.request-assistant', $test->id) }}" id="requestAssistantForm"
                      method="POST">
                    @csrf
                    <button class="btn btn-outline-success" type="submit">Přihlásit se jako asistent</button>
                </form>
            @else
                @if ($askedAssistants->contains($auth->getKey()))
                    <button class="btn btn-success" disabled>Požádali jste o to být asistent</button>
                @endif
            @endcan

            @can(\App\Enums\PermissionsEnum::REQUEST_STUDENT, $test)
                <form action="{{ route('tests.request-student', $test->id) }}" id="requestStudentForm"
                      method="POST">
                    @csrf
                    <button class="btn btn-outline-success" type="submit">Přihlásit se jako student</button>
                </form>
            @endcan
        </div>
        <div>
            <div id="mainLabels">
                @component('app.components.panel-label', ['active' => true, 'target' => 'panelInfo', 'parent' => 'mainLabels'])
                    @slot('label')
                        Základní informace
                    @endslot
                @endcomponent

                @if($auth->role >= \App\Enums\RolesEnum::ROLE_ASSISTANT)
                    @component('app.components.panel-label', ['target' => 'panelGroups', 'parent' => 'mainLabels'])
                        @slot('label')
                            Skupiny otázek
                        @endslot
                    @endcomponent
                @endif

                @if($solution)
                    @component('app.components.panel-label', ['target' => 'panelSolution', 'parent' => 'mainLabels'])
                        @slot('label')
                            Moje řešení
                        @endslot
                    @endcomponent
                @endif

                @component('app.components.panel-label', ['target' => 'panelStudents', 'parent' => 'mainLabels'])
                    @slot('label')
                        {{ __('labels.students') }}
                    @endslot
                @endcomponent
            </div>
            <div id="testFormPanels">
                @component('app.components.form-panel', ['id' => 'panelInfo', 'active' => true, 'parent' => 'testFormPanels'])
                    <div class="row">
                        <div class="table-responsive col-12 col-lg-6">
                            <table class="table table-hover test-info-table">
                                <tbody>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.professor') }}</th>
                                    <td><a href="{{ route('users.show', $test->professor_id) }}"
                                           class="text-info">{{ $test->professor->name }}</a></td>
                                </tr>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.subject') }}</th>
                                    <td>{{ $test->subject }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.description') }}</th>
                                    <td>{{ $test->description }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.start') }}</th>
                                    <td>{{ $test->start_date->format('d. m. Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.time_limit') }}</th>
                                    <td>{{ trans_choice('labels.minutes_value', $test->time_limit, ['val' => $test->time_limit]) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.questions_number') }}</th>
                                    <td>{{ $test->questions_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" width="200">{{ __('labels.assistants') }}</th>
                                    <td>
                                        @foreach($assistants as $assistant)
                                            <a href="{{ route('users.show', $assistant->id) }}"
                                               class="text-info">{{ $assistant->name }}</a><br>
                                        @endforeach
                                    </td>
                                </tr>
                                @can(\App\Enums\PermissionsEnum::START_TEST, $test)
                                    <tr>
                                        <th scope="row" width="200"></th>
                                        <td>
                                            <form action="{{ route('tests.start', $test->id) }}" method="POST">
                                                @csrf
                                                @if ($auth->role >= \App\Enums\RolesEnum::ROLE_ASSISTANT)
                                                    @include('partials.input-select', [
                                                       'name' => 'group_id',
                                                       'label' => __('labels.pick_group'),
                                                       'value' => old('group_id'),
                                                       'required' => true,
                                                       'options' => $test->groups->mapWithKeys(static function (\App\Models\Group $group): array {
                                                            return [$group->id => $group->name];
                                                        }),
                                                   ])
                                                @endif
                                                <button type="submit"
                                                        class="btn btn-info">{{ __('labels.startverb') }}</button>
                                                @if ($errors->any())
                                                    @foreach($errors->all() as $error)
                                                        <div class="text-danger">{{ $error }}</div>
                                                    @endforeach
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @elsecan(\App\Enums\PermissionsEnum::SOLVE_TEST, $test)
                                    <tr>
                                        <th scope="row" width="200"></th>
                                        <td>
                                            <a href="{{ route('tests.solution', $test->id) }}" class="btn btn-info">
                                                {{ __('labels.solve') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endcan
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endcomponent

                @if($auth->role >= \App\Enums\RolesEnum::ROLE_PROFESSOR || $assistants->contains($auth->getKey()))
                    @component('app.components.form-panel', ['id' => 'panelGroups', 'parent' => 'testFormPanels'])
                        <div>
                            @include('app.tests.show_groups')
                        </div>
                    @endcomponent
                @endif

                @if($solution)
                    @component('app.components.form-panel', ['id' => 'panelSolution', 'parent' => 'testFormPanels'])
                        <div>
                            @include('app.tests.show_solution')
                        </div>
                    @endcomponent
                @endif

                @component('app.components.form-panel', ['id' => 'panelStudents', 'parent' => 'testFormPanels'])
                    <div>
                        <data-table :fields="{{ $studentsTable->getColumnsJson() }}"
                                    api-url="{{ $studentsTable->getBaseApiUrl() }}"
                                    filters-url="{{ route('tests.students.json.filters', $test->id) }}">
                            <template slot="test_student.accepted" slot-scope="{rowData}">
                                <span>
                                    <i v-if="rowData['test_student.accepted']" class="fas fa-check-circle fa-2x text-success"></i>
                                    <i v-else class="fas fa-times-circle fa-2x text-danger"></i>
                                </span>
                            </template>
                            <template slot="nickname" slot-scope="{rowData}">
                                <a :href="`/users/${rowData.id}`" class="text-info">@{{ rowData.nickname }}</a>
                            </template>
                        </data-table>
                    </div>
                @endcomponent
            </div>
        </div>
    @else
        <h4 class="h4 text-primary">Test není připraven</h4>
        @if ($test->professor_id === $auth->id)
            <p class="text-danger">Zkontrolujte zda je vytvořena alespoň jedna skupina a každá ma při nejmenším
                specifikovanej
                počet otázek.</p>
        @endif
    @endif
@endsection
