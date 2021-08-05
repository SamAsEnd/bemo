@extends('layouts.app')

@section('content')

    <div class="authentication">
        <form class="authentication__form" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input">
                <label for="name" class="input__label">Name</label>

                <input id="name" type="text" class="input__control @error('name') input__control--invalid @enderror"
                       name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                @error('name')
                <span class="input__error">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="input">
                <label for="email" class="input__label">E-Mail Address</label>

                <input id="email" type="email" class="input__control @error('email') input__control--invalid @enderror"
                       name="email" value="{{ old('email') }}" required autocomplete="email">

                @error('email')
                <span class="input__error">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="input">
                <label for="password" class="input__label">Password</label>

                <div class="col-md-6">
                    <input id="password" type="password" class="input__control @error('password') input__control--invalid @enderror"
                           name="password" required autocomplete="new-password">

                    @error('password')
                    <span class="input__error">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="input">
                <label for="password-confirm" class="input__label">Confirm Password</label>

                <div class="col-md-6">
                    <input id="password-confirm" type="password" class="input__control" name="password_confirmation"
                           required autocomplete="new-password">
                </div>
            </div>

            <div class="input">
                <button type="submit" class="button">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>

@endsection
