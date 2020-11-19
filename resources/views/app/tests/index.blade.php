@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Tables\UsersTable $table
     */
@endphp

@extends('layouts.app')

@section('content')
    <div>
        @can(\App\Enums\PermissionsEnum::CREATE, \App\Models\User::class)
            <div class="actions text-right mb-4">
                <a href="{{ route('tests.create') }}" class="btn btn-info">
                    <i class="fas fa-plus-circle"></i> {{ trans('labels.new_test') }}
                </a>
            </div>
        @endcan
        <data-table :fields="{{ $table->getColumnsJson() }}"
                    api-url="{{ $table->getBaseApiUrl() }}"
                    filters-url="{{ route('tests.json.filters') }}">
        </data-table>
    </div>
@endsection
