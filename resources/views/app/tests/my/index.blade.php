@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Tables\MyTestsTable $table
     */
@endphp

@extends('layouts.app')

@section('content')
    <div>
        <data-table :fields="{{ $table->getColumnsJson() }}"
                    api-url="{{ $table->getBaseApiUrl() }}"
                    filters-url="{{ route('tests.my.json.filters') }}">
        </data-table>
    </div>
@endsection
