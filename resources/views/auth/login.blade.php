@extends('layouts.main')

@section('content')
    <div class="bg-primary login-page-bg d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-warning text-center text-primary">
                            <h1 class="m-0">{{ config('app.name', 'Laravel') }}</h1>
                            <p class="m-0">Aplikace pro tvorbu, plnění a hodnocení testových zkoušek</p>
                        </div>

                        <div class="card-body text-center">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="email" class="text-md-right">
                                        {{ __('labels.login_username') }}
                                    </label>

                                    <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <input id="email"
                                               type="text"
                                               class="form-control text-center @error('email') is-invalid @enderror"
                                               name="email"
                                               value="{{ old('email') }}"
                                               required
                                               autocomplete="username"
                                               autofocus>

                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                </div>

                                <div class="form-group">
                                    <label for="password"
                                           class="text-md-right">{{ __('labels.login_password') }}</label>

                                    <div class="row justify-content-center">
                                        <div class="col-md-6">
                                            <input id="password" type="password"
                                                   class="form-control text-center @error('password') is-invalid @enderror"
                                                   name="password" required autocomplete="current-password">

                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>


                                    </div>
                                </div>

                                <div class="form-group row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                   id="remember" {{ old('remember') ? 'checked' : '' }}>

                                            <label class="form-check-label" for="remember">
                                                {{ __('labels.remember_me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-0 justify-content-center">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('labels.login') }}
                                        </button>
                                        <p class="text-muted m-0">Po přihlášení systém automaticky rospozná Vaši roli</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
