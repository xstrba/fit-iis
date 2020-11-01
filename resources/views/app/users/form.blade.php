@php
/**
 * @var \App\Models\User $auth
 * @var \App\Models\User $user
 */
@endphp

@extends('layouts.app')

@section('content')
    <form action="{{ $user->exists ? route('users.update', $user->id) : route('users.store') }}"
          method="POST">
        @csrf

        @if ($user->exists)
            @method('PUT')
        @endif

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'first_name',
                'label' => __('labels.first_name'),
                'value' => old('first_name', $user->first_name),
                'required' => true,
            ])
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'last_name',
                'label' => __('labels.last_name'),
                'value' => old('last_name', $user->last_name),
            ])
        </div>

        <div class="row">
            @include('partials.input-email', [
                'classes' => 'col-md-6',
                'name' => 'email',
                'label' => __('labels.email'),
                'value' => old('email', $user->email),
                'required' => true,
            ])
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'nickname',
                'label' => __('labels.nickname'),
                'value' => old('nickname', $user->nickname),
                'required' => true,
            ])
        </div>

        <div class="row">
            @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'gender',
                    'label' => __('labels.gender'),
                    'value' => old('gender', $user->gender),
                    'required' => true,
                    'options' => \App\Enums\GendersEnum::instance()->getList(),
                ])
            @include('partials.input-date', [
                    'classes' => 'col-md-6',
                    'name' => 'birth',
                    'label' => __('labels.birth_date'),
                    'value' => old('birth', optional($user->birth)->format('Y-m-d')),
                    'required' => true,
                ])
        </div>

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'street',
                'label' => __('labels.street'),
                'value' => old('street', $user->street),
                'required' => true,
                'placeholder' => trans('common.eg') . ': Masarykova',
            ])
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'house_number',
                'label' => __('labels.house_number'),
                'value' => old('house_number', $user->house_number),
                'required' => true,
                'placeholder' => trans('common.eg') . ': 123/45',
            ])
        </div>

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'city',
                'label' => __('labels.city'),
                'value' => old('city', $user->city),
                'required' => true,
                'placeholder' => trans('common.eg') . ': Brno',
            ])
            @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'country',
                    'label' => __('labels.country'),
                    'value' => old('country', $user->country),
                    'required' => true,
                    'options' => \App\Enums\CountriesEnum::instance()->getList(app()->getLocale()),
                ])
        </div>

        <div class="row">
            @include('partials.input-text', [
                'classes' => 'col-md-6',
                'name' => 'phone',
                'label' => __('labels.phone'),
                'value' => old('phone', $user->phone),
                'required' => true,
                'placeholder' => trans('common.eg') . ': +420 123 456 789',
            ])
            @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'language',
                    'label' => __('labels.preferred_language'),
                    'value' => old('language', $user->language),
                    'required' => true,
                    'options' => \App\Enums\LanguagesEnum::instance()->getList(),
                ])
        </div>

        @if(!$user->exists)
            <div class="row">
                @include('partials.input-password', [
                    'classes' => 'col-md-6',
                    'name' => 'password',
                    'label' => __('labels.password'),
                    'required' => true,
                ])
                @include('partials.input-password', [
                    'classes' => 'col-md-6',
                    'name' => 'password_confirmation',
                    'label' => __('labels.password_confirmation'),
                ])
            </div>
        @endif

        <div class="row">
            @include('partials.input-select', [
                'classes' => 'col-md-6',
                'name' => 'role',
                'label' => __('labels.role'),
                'value' => old('role', $user->role),
                'required' => true,
                'options' => \array_filter([
                    \App\Enums\RolesEnum::ROLE_STUDENT => __('roles.' . \App\Enums\RolesEnum::ROLE_STUDENT),
                    \App\Enums\RolesEnum::ROLE_ASSISTANT => __('roles.' . \App\Enums\RolesEnum::ROLE_ASSISTANT),
                    \App\Enums\RolesEnum::ROLE_PROFESSOR => __('roles.' . \App\Enums\RolesEnum::ROLE_PROFESSOR),
                    \App\Enums\RolesEnum::ROLE_ADMINISTRATOR => __('roles.' . \App\Enums\RolesEnum::ROLE_ADMINISTRATOR),
                ], static function (int $key) use ($auth): bool {return $auth->role >= $key;}, ARRAY_FILTER_USE_KEY),
            ])
        </div>

        <button type="submit" class="btn btn-primary mb-2">{{ $user->exists ? trans('labels.update') : trans('labels.create') }}</button>
    </form>
@endsection
