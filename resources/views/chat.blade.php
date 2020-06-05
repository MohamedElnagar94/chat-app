@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <chat-app-component user_id="{{\Illuminate\Support\Facades\Auth::id()}}"></chat-app-component>
            </div>
        </div>
    </div>
@endsection