@php
/**
 * @var \App\Models\User $auth
 * @var \App\Models\User $user
 */
@endphp

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <ul class="list-group list-group-flush bg-white">
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.name') }}:
                    </span>
                    {{ $user->name }}
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.username') }}:
                    </span>
                    {{ $user->nickname }}
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.gender') }}:
                    </span>
                    {{ trans('labels.' . $user->gender) }}
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.birth_date') }}:
                    </span>
                    {{ $user->birth->format('d. m. Y') }} ({{ $user->age . ' ' . trans('common.years') }})
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{  trans('roles.' . $user->role) }}
                    </span>
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.email') }}:
                    </span>
                    {{ $user->email }}
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.phone') }}:
                    </span>
                    {{ $user->phone }}
                </li>
                <li class="list-group-item">
                    <span class="text-primary font-weight-bold">
                        {{ trans('labels.address') }}:
                    </span>
                    {{ $user->address }}
                </li>
            </ul>
        </div>
    </div>
@endsection
