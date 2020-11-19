@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Models\Test $test
     * @var \Illuminate\Support\Collection $professors
     */
@endphp

@extends('layouts.app')

@section('content')
    <form action="{{ $test->exists ? route('tests.update', $test->id) : route('tests.store') }}"
          method="POST" onsubmit="event.preventDefault()" id="testForm">
        @csrf

        @if ($test->exists)
            @method('PUT')
        @endif

        <div id="mainLabels">
            @component('app.components.panel-label', ['active' => true, 'target' => 'panelInfo', 'parent' => 'mainLabels'])
                @slot('label')
                    Základní informace
                @endslot
            @endcomponent

            @component('app.components.panel-label', ['target' => 'panelConfig', 'parent' => 'mainLabels'])
                @slot('label')
                    Konfigurace
                @endslot
            @endcomponent

            @if ($test->exists)
                @component('app.components.panel-label', ['target' => 'panelAssistant', 'parent' => 'mainLabels'])
                    @slot('label')
                        Asistenti
                    @endslot
                @endcomponent
            @endif

            @if ($test->exists)
                @component('app.components.panel-label', ['target' => 'panelGroups', 'parent' => 'mainLabels'])
                    @slot('label')
                        Skupiny otázek
                    @endslot
                @endcomponent
            @endif

            <div id="testFormPanels">
                @component('app.components.form-panel', ['id' => 'panelInfo', 'active' => true, 'parent' => 'testFormPanels'])
                    <div class="row">
                        @include('partials.input-select', [
                                'classes' => 'col-md-6',
                                'name' => 'subject',
                                'label' => __('labels.subject'),
                                'value' => old('subject', $test->subject),
                                'required' => true,
                                'options' => \App\Enums\SubjectsEnum::instance()->getList(),
                            ])
                        @include('partials.input-text', [
                            'classes' => 'col-md-6',
                            'name' => 'name',
                            'label' => __('labels.title'),
                            'autocomplete' => 'off',
                            'value' => old('name', $test->name),
                            'required' => true,
                        ])
                    </div>

                    <div class="row">
                        @include('partials.input-textbox', [
                                'classes' => 'col-sm-12',
                                'name' => 'description',
                                'label' => __('labels.description'),
                                'value' => old('description', $test->description),
                                'required' => false,
                            ])
                    </div>

                    <div class="row">
                        @include('partials.input-select', [
                                'classes' => 'col-md-6',
                                'name' => 'professor_id',
                                'label' => __('labels.professor'),
                                'value' => old('professor_id', $test->professor_id),
                                'required' => true,
                                'options' => $professors->mapWithKeys(static function (\App\Models\User $professor): array {
                                    return [$professor->id => $professor->name];
                                }),
                            ])
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary mb-2" onclick="document.getElementById('testForm').submit()">
                            {{ $test->exists ? trans('labels.update') : trans('labels.create') }}
                        </button>
                    </div>
                @endcomponent

                @component('app.components.form-panel', ['id' => 'panelConfig', 'parent' => 'testFormPanels'])
                    <div class="row">
                        @include('partials.input-date', [
                            'classes' => 'col-md-6',
                            'name' => 'start_date',
                            'label' => __('labels.start'),
                            'value' => old('start_date', optional($test->start_date)->format('Y-m-d\TH:i')),
                            'required' => true,
                            'time' => true,
                        ])
                        @include('partials.input-number', [
                            'classes' => 'col-md-6',
                            'name' => 'time_limit',
                            'label' => __('labels.time_limit') . ' (' . __('labels.minutes') . ')',
                            'value' => old('time_limit', $test->time_limit),
                            'required' => true,
                            'min' => 0,
                        ])
                    </div>

                    <div class="row">
                        @include('partials.input-number', [
                            'classes' => 'col-md-6',
                            'name' => 'questions_number',
                            'label' => __('labels.questions_number'),
                            'value' => old('questions_number', $test->questions_number),
                            'required' => true,
                            'min' => 1,
                        ])
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary mb-2" onclick="document.getElementById('testForm').submit()">
                            {{ $test->exists ? trans('labels.update') : trans('labels.create') }}
                        </button>
                    </div>
                @endcomponent

                @component('app.components.form-panel', ['id' => 'panelAssistant', 'parent' => 'testFormPanels'])
                    <assistants-list :assistants="{{ $test->assistants->toJson() }}"
                                     :test="{{ $test->toJson() }}"></assistants-list>
                @endcomponent

                @component('app.components.form-panel', ['id' => 'panelGroups', 'parent' => 'testFormPanels'])
                    <groups-form :groups="{{ $test->groups->toJson() }}"
                                     :test="{{ $test->toJson() }}"></groups-form>
                @endcomponent
            </div>
        </div>
    </form>
@endsection
