@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Models\Test $test
     * @var \App\Models\User[]|\Illuminate\Support\Collection $assistants
     * @var \App\Models\User[]|\Illuminate\Support\Collection $askedAssistants
    */
@endphp

@extends('layouts.app')

@section('content')
    <div class="mb-2">
        @can(\App\Enums\PermissionsEnum::REQUEST_ASSISTANT, $test)
            <form action="{{ route('tests.request-assistant', $test->id) }}" id="requestAssistantForm" method="POST">
                @csrf
                <button class="btn btn-outline-success" type="submit">Přihlásit se jako asistent</button>
            </form>
        @else
            @if ($askedAssistants->contains($auth->getKey()))
                <button class="btn btn-success" disabled>Požádali jste o to být asistent</button>
            @endif
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
        </div>
    </div>
@endsection
