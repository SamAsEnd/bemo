@extends('layouts.app')

@section('content')
    <div class="authentication">
        <form class="authentication__form" method="POST" action="{{ route('password.confirm') }}">
            @csrf

            {{ __('Please confirm your password before continuing.') }}

            <div class="input">
                <label for="password" class="input__label">Password</label>

                <input id="password" type="password"
                       class="input__control @error('password') input__control--invalid @enderror" name="password"
                       required autocomplete="current-password">

                @error('password')
                <span class="input__error" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <hr>

            <div class="input">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection
