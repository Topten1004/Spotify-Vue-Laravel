@extends('vendor.installer.layouts.master')

@section('template_title')
Create admin account
@endsection

@section('title')
    <i class="fa fa-user" aria-hidden="true"></i>
    Create admin account
@endsection

@section('container')
<form method="post" action="{{  route('LaravelInstaller::createAdminAndFinish') }}" class="tabs-wrap">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
    <label for="email">
        Email
    </label>
    <input type="text" name="email" id="email"placeholder="Your email" />
    @if ($errors->has('email'))
        <span class="error-block">
            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
            {{ $errors->first('email') }}
        </span>
    @endif
</div>

<div class="form-group {{ $errors->has('password') ? ' has-error ' : '' }}">
    <label for="password">
        Password
    </label>
    <input type="password" name="password" id="password"  placeholder="Password" />
    @if ($errors->has('password'))
        <span class="error-block">
            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
            {{ $errors->first('password') }}
        </span>
    @endif
</div>

<div class="form-group {{ $errors->has('confirm_password') ? ' has-error ' : '' }}">
    <label for="confirm_password">
        Confirm password
    </label>
    <input type="password" name="password_confirmation" id="confirm_password"  placeholder="Confirm password" />
    @if ($errors->has('confirm_password'))
        <span class="error-block">
            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
            {{ $errors->first('confirm_password') }}
        </span>
    @endif
    <div class="buttons">
        <button class="button" type="submit" id="finalisation-button" onclick="hideMe()">
            Install
            <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
        </button>
        <div id="message">
            This might take a while. Please wait...
        </div>
        <div id="loading-status">
            <div class="loading-container">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
              </div>
        </div>
    </div>
</div>

</form>
<style>
    .loading-container {
    width: 50px;
    height: 30px;
    position: relative;
    margin: 0 auto;
}
.loading-container span {
    width: 5px;
    height: 8px;
    position: absolute;
    bottom: 0;
    left: 0;
    display: block;
    background-color: #9B5986;
    animation: Loading 1s infinite ease-in-out; 
}
.loading-container span:nth-of-type(2) { left: 0px; animation-delay: .5s; }
.loading-container span:nth-of-type(3) { left: 10px; animation-delay: .1s; }
.loading-container span:nth-of-type(4) { left: 20px; animation-delay: .2s; }
.loading-container span:last-of-type { left: 30px; animation-delay: .4s; }

@keyframes Loading {
    0% { height: 10px; transform: scaleY(); background-color: #9B59B6; }
    50% { height: 10px; transform: scaleY(3.4); background-color: #3498D6; }
    100% { height: 10px; transform: scaleY(); background-color: #9B59B6; }
}
    #loading-status{
        display: none;
    }
    #message {
        display: none;
    }
</style>
<script>
    var finalisationButton = document.getElementById('finalisation-button');
    var loadingStatus = document.getElementById('loading-status');
    function hideMe() {
        finalisationButton.style.display = 'none';
        loadingStatus.style.display = 'block';
        message.style.display = 'block';
    }
</script>
@endsection