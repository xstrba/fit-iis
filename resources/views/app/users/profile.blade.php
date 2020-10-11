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
            @include('partials.input-password', [
                'classes' => 'col-md-6',
                'name' => 'password',
                'label' => __('labels.password'),
            ])
            @include('partials.input-password', [
                'classes' => 'col-md-6',
                'name' => 'password_confirmation',
                'label' => __('labels.password_confirmation'),
            ])
        </div>

        @if ($auth->nickname !== 'root')
            <div class="row">
                @include('partials.input-select', [
                    'classes' => 'col-md-6',
                    'name' => 'role',
                    'label' => __('labels.role'),
                    'value' => old('role', $auth->role),
                    'required' => true,
                    'options' => \array_filter([
                        \App\Enums\RolesEnum::ROLE_STUDENT => __('roles.' . \App\Enums\RolesEnum::ROLE_STUDENT),
                        \App\Enums\RolesEnum::ROLE_ASSISTANT => __('roles.' . \App\Enums\RolesEnum::ROLE_ASSISTANT),
                        \App\Enums\RolesEnum::ROLE_PROFESSOR => __('roles.' . \App\Enums\RolesEnum::ROLE_PROFESSOR),
                        \App\Enums\RolesEnum::ROLE_ADMINISTRATOR => __('roles.' . \App\Enums\RolesEnum::ROLE_ADMINISTRATOR),
                    ], static function (int $key) use ($auth): bool {return $auth->role >= $key;}, ARRAY_FILTER_USE_KEY),
                ])
            </div>
        @endif

        <button type="submit" class="btn btn-primary mb-2">{{ __('labels.update') }}</button>
    </form>
@endsection
