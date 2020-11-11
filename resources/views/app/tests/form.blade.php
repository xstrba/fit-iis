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
          method="POST">
        @csrf

        @if ($test->exists)
            @method('PUT')
        @endif

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

        <div class="row my-2">
            <h3 class="h3 col-12 text-info">{{ __('labels.configuration') }}</h3>
        </div>

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

        <div class="row">
{{--            @include('partials.input-text', [--}}
{{--                'classes' => 'col-md-6',--}}
{{--                'name' => 'city',--}}
{{--                'label' => __('labels.city'),--}}
{{--                'value' => old('city', $test->city),--}}
{{--                'required' => true,--}}
{{--                'placeholder' => trans('common.eg') . ': Brno',--}}
{{--            ])--}}
{{--            @include('partials.input-select', [--}}
{{--                    'classes' => 'col-md-6',--}}
{{--                    'name' => 'country',--}}
{{--                    'label' => __('labels.country'),--}}
{{--                    'value' => old('country', $test->country),--}}
{{--                    'required' => true,--}}
{{--                    'options' => \App\Enums\CountriesEnum::instance()->getList(app()->getLocale()),--}}
{{--                ])--}}
        </div>

        <div class="row">
{{--            @include('partials.input-text', [--}}
{{--                'classes' => 'col-md-6',--}}
{{--                'name' => 'phone',--}}
{{--                'label' => __('labels.phone'),--}}
{{--                'value' => old('phone', $test->phone),--}}
{{--                'required' => true,--}}
{{--                'placeholder' => trans('common.eg') . ': +420 123 456 789',--}}
{{--            ])--}}
{{--            @include('partials.input-select', [--}}
{{--                    'classes' => 'col-md-6',--}}
{{--                    'name' => 'language',--}}
{{--                    'label' => __('labels.preferred_language'),--}}
{{--                    'value' => old('language', $test->language),--}}
{{--                    'required' => true,--}}
{{--                    'options' => \App\Enums\LanguagesEnum::instance()->getList(),--}}
{{--                ])--}}
        </div>

        @if(!$test->exists)
            <div class="row">
{{--                @include('partials.input-password', [--}}
{{--                    'classes' => 'col-md-6',--}}
{{--                    'name' => 'password',--}}
{{--                    'label' => __('labels.password'),--}}
{{--                    'required' => true,--}}
{{--                ])--}}
{{--                @include('partials.input-password', [--}}
{{--                    'classes' => 'col-md-6',--}}
{{--                    'name' => 'password_confirmation',--}}
{{--                    'label' => __('labels.password_confirmation'),--}}
{{--                ])--}}
            </div>
        @endif

        <button type="submit" class="btn btn-primary mb-2">{{ $test->exists ? trans('labels.update') : trans('labels.create') }}</button>
    </form>
@endsection
