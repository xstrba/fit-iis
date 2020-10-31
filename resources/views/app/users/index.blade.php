@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Tables\UsersTable $table
     */
@endphp

@extends('layouts.app')

@section('content')
    <div>
        <div class="actions text-right mb-4">
            <a href="{{ route('users.create') }}" class="btn btn-info">
                <i class="fas fa-plus-circle"></i> {{ trans('labels.new_user') }}
            </a>
        </div>
        <data-table :fields="{{ $table->getColumnsJson() }}"
                    api-url="{{ $table->getBaseApiUrl() }}"
                    :filters="{{ $table->getFiltersJson() }}">
        </data-table>
    </div>
@endsection
