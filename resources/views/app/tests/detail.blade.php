@php
    /**
     * @var \App\Models\User $auth
     * @var \App\Models\Test $test
     */
@endphp

@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="table-responsive col-12 col-lg-6">
            <table class="table table-hover test-info-table">
                <tbody>
                <tr>
                    <th scope="row" width="200">{{ __('labels.professor') }}</th>
                    <td><a href="{{ route('users.show', $test->professor_id) }}" class="text-info">{{ $test->professor->name }}</a></td>
                </tr>
                <tr>
                    <th scope="row" width="200">{{ __('labels.subject') }}</th>
                    <td>{{ $test->subject }}</td>
                </tr>
                <tr>
                    <th scope="row" width="200">{{ __('labels.description') }}</th>
                    <td>{{ $test->description }}</td>
                </tr>
                <tr>
                    <th scope="row" width="200">{{ __('labels.start') }}</th>
                    <td>{{ $test->start_date->format('d. m. Y H:i:s') }}</td>
                </tr>
                <tr>
                    <th scope="row" width="200">{{ __('labels.time_limit') }}</th>
                    <td>{{ trans_choice('labels.minutes_value', $test->time_limit, ['val' => $test->time_limit]) }}</td>
                </tr>
                <tr>
                    <th scope="row" width="200">{{ __('labels.questions_number') }}</th>
                    <td>{{ $test->questions_number }}</td>
                </tr>
                <tr>
                    <th scope="row" width="200">{{ __('labels.assistants') }}</th>
                    <td>
                        @foreach($test->assistants as $assistant)
                            <a href="{{ route('users.show', $assistant->id) }}" class="text-info">{{ $assistant->name }}</a><br>
                        @endforeach
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
