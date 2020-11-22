@php
/**
 * @var \App\Models\User $auth
 */
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-info">
                <div class="card-header bg-info">
                    <h5 class="h5 text-white m-0">{{ __('labels.welcome') }}, {{ $auth->name }}</h5>
                </div>

                <div class="card-body">
                    <span class="font-weight-bold">{{ __('labels.logged_as') }}</span>: {{ __('roles.' . $auth->role) }}
                    <div>
                        <span class="font-weight-bold">{{ __('labels.inspiring_quote') }}</span>:
                        <p>{{ \Illuminate\Foundation\Inspiring::quote() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
