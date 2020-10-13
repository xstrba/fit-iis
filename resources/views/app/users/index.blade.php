@php
/**
 * @var \App\Models\User $auth
 * @var \App\Tables\UsersTable $table
 */
@endphp

@extends('layouts.app')

@section('content')
    <div>
        <data-table :fields="{{ $table->getColumnsJson() }}" api-url="{{ $table->getBaseApiUrl() }}">
        </data-table>
    </div>
@endsection
