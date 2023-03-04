@extends('vendor.installer.layouts.master')

@section('template_title')
    Installation Finished
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    Installation Finished
@endsection

@section('container')
    <div class="buttons">
        <a href="{{ url('/login') }}" class="button">Login</a>
    </div>
@endsection
