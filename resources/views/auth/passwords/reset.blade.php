@extends('layouts.app')

@section('content')
    <div class="authentication">
        <form class="authentication__form" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="input">
                <label for="email" class="input__label">E-Mail Address</label>

                <input id="email" type="email"
                       class="input__control @error('email') input__control--invalid @enderror" name="email"
                       value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                <span class="input__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input">
                <label for="password" class="input__label">Password</label>

                <input id="password" type="password"
                       class="input__control @error('password') input__control--invalid @enderror"
                       name="password" required autocomplete="new-password">

                @error('password')
                <span class="input__error" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="input">
                <label for="password-confirm" class="input__label">Confirm Password</label>

                <input id="password-confirm" type="password" class="input__control" name="password_confirmation"
                       required autocomplete="new-password">
            </div>

            <div class="input">
                <button type="submit" class="button">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
@endsection
