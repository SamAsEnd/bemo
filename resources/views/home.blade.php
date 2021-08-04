@extends('layouts.app')

@section('content')
    <columns></columns>

    <div class="container">
        <div class="row justify-content-end">
            <div class="col-1 mt-5">
                <a href="{{ route('export-db') }}" class="rounded-circle p-4 btn btn-outline-dark float-right">
                    Export Database
                </a>
            </div>
        </div>
    </div>
@endsection
