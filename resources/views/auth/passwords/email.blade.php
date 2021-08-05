@extends('layouts.app')

@section('content')
    <div class="authentication">
        <form class="authentication__form" method="POST" action="{{ route('password.email') }}">
            @csrf

            @if (session('status'))
                <div class="alert alert--success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="input">
                <label for="email" class="input__label">E-Mail Address</label>

                <input id="email" type="email" class="input__control @error('email') input__control--invalid @enderror"
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                <span class="input__error" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input">
                <button type="submit" class="button">
                    Send Password Reset Link
                </button>
            </div>
        </form>
    </div>
@endsection
