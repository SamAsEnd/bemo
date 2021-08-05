@extends('layouts.app')

@section('content')

    <div class="authentication">
        <div class="authentication__form authentication__form--narrow">
            <h2>Verify Your Email Address</h2>

            @if (session('resent'))
                <p class="alert alert--success">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </p>
            @endif

            {{ __('Before proceeding, please check your email for a verification link.') }}
            {{ __('If you did not receive the email') }},

            <hr>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="button">click here to request another</button>.
            </form>
        </div>
    </div>
</div>
@endsection
