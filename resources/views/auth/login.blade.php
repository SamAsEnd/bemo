@extends('layouts.app')

@section('content')

    <div class="authentication">
        <form class="authentication__form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input">
                <label for="email" class="input__label">E-Mail Address</label>

                <input id="email" type="email" class="input__control @error('email') input__control--invalid @enderror"
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                <span class="input__error">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="input">
                <label for="password" class="input__label">Password</label>

                <input id="password" type="password"
                       class="input__control @error('password') input__control--invalid @enderror"
                       name="password" required autocomplete="current-password">

                @error('password')
                <span class="input__">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="input">
                <label class="input__label" for="remember">
                    Remember Me

                    <input class="input__control" type="checkbox" name="remember"
                           id="remember" {{ old('remember') ? 'checked' : '' }}>
                </label>
            </div>

            <div class="input">
                <button type="submit" class="button">
                    {{ __('Login') }}
                </button>

                <hr/>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

@endsection
