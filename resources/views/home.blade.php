@extends('layouts.app')

@section('content')
    <columns></columns>

    <div class="export">
        <a href="{{ route('export-db') }}" class="export__button">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none"
                 stroke="#465A5A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                <polyline points="16 6 12 2 8 6"></polyline>
                <line x1="12" y1="2" x2="12" y2="15"></line>
            </svg>
        </a>
    </div>
@endsection
