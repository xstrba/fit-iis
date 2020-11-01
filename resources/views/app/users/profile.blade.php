@php
/**
 * @var \App\Models\User $auth
 */
@endphp

@extends('layouts.app')

@section('content')
    <form action="{{ route('users.update', $auth->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'first_name',
                'label' => __('labels.first_name'),
                'value' => old('first_name', $auth->first_name),
                'required' => true,
            ])
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'last_name',
                'label' => __('labels.last_name'),
                'value' => old('last_name', $auth->last_name),
            ])
        </div>

        <div class="row">
            @include('partials.input-email', [
                'classes' => 'col-md-6',
                'name' => 'email',
                'label' => __('labels.email'),
                'value' => old('email', $auth->email),
                'required' => true,
            ])
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'nickname',
                'label' => __('labels.nickname'),
                'value' => old('nickname', $auth->nickname),
                'required' => true,
            ])
        </div>

        <div class="row">
            @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'gender',
                    'label' => __('labels.gender'),
                    'value' => old('gender', $auth->gender),
                    'required' => true,
                    'options' => \App\Enums\GendersEnum::instance()->getList(),
                ])
            @include('partials.input-date', [
                    'classes' => 'col-md-6',
                    'name' => 'birth',
                    'label' => __('labels.birth_date'),
                    'value' => old('birth', $auth->birth->format('Y-m-d')),
                    'required' => true,
                ])
        </div>

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'street',
                'label' => __('labels.street'),
                'value' => old('street', $auth->street),
                'required' => true,
                'placeholder' => trans('common.eg') . ': Masarykova',
            ])
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'house_number',
                'label' => __('labels.house_number'),
                'value' => old('house_number', $auth->house_number),
                'required' => true,
                'placeholder' => trans('common.eg') . ': 123/45',
            ])
        </div>

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'city',
                'label' => __('labels.city'),
                'value' => old('city', $auth->city),
                'required' => true,
                'placeholder' => trans('common.eg') . ': Brno',
            ])
            @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'country',
                    'label' => __('labels.country'),
                    'value' => old('country', $auth->country),
                    'required' => true,
                    'options' => \App\Enums\CountriesEnum::instance()->getList(app()->getLocale()),
                ])
        </div>

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'phone',
                'label' => __('labels.phone'),
                'value' => old('phone', $auth->phone),
                'required' => true,
                'placeholder' => trans('common.eg') . ': +420 123 456 789',
            ])
            @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'language',
                    'label' => __('labels.preferred_language'),
                    'value' => old('language', $auth->language),
                    'required' => true,
                    'options' => \App\Enums\LanguagesEnum::instance()->getList(),
                ])
        </div>

        <div class="row">
            @include('partials.input-password', [
                'classes' => 'col-md-6',
                'name' => 'password',
                'label' => __('labels.new_password'),
            ])
            @include('partials.input-password', [
                'classes' => 'col-md-6',
                'name' => 'password_confirmation',
                'label' => __('labels.password_confirmation'),
            ])
        </div>

        @if ($auth->nickname !== 'root' && $auth->role > \App\Enums\RolesEnum::ROLE_STUDENT)
            <div class="row">
                @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'role',
                    'label' => __('labels.role'),
                    'value' => old('role', $auth->role),
                    'required' => true,
                    'options' => \array_filter([
                        \App\Enums\RolesEnum::ROLE_STUDENT => trans('roles.' . \App\Enums\RolesEnum::ROLE_STUDENT),
                        \App\Enums\RolesEnum::ROLE_ASSISTANT => trans('roles.' . \App\Enums\RolesEnum::ROLE_ASSISTANT),
                        \App\Enums\RolesEnum::ROLE_PROFESSOR => trans('roles.' . \App\Enums\RolesEnum::ROLE_PROFESSOR),
                        \App\Enums\RolesEnum::ROLE_ADMINISTRATOR => trans('roles.' . \App\Enums\RolesEnum::ROLE_ADMINISTRATOR),
                    ], static function (int $key) use ($auth): bool {return $auth->role >= $key;}, ARRAY_FILTER_USE_KEY),
                ])
            </div>
        @endif

        <button type="submit" class="btn btn-primary mb-2">{{ __('labels.update') }}</button>
    </form>
@endsection
