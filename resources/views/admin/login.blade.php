<?php
use App\IekModel\Version1_0\IekModel;

$transFile      = 'login';
$login          = IekModel::strTrans('login', $transFile);
$account        = IekModel::strTrans('account', $transFile);
$password       = IekModel::strTrans('password', $transFile);
$email          = IekModel::strTrans('email', $transFile);
$forgotPassword = IekModel::strTrans('forgotPassword', $transFile);
$toLogin        = IekModel::strTrans('toLogin', $transFile);
$sendCode       = IekModel::strTrans('sendCode', $transFile);
$notice         = IekModel::strTrans('notice', $transFile);
?>
<input id="token" type="hidden" name="_token" value="{{csrf_token()}}">
<form id="login-form" class="form-vertical login-form" >
    <div class="control-group normal_text"><h3><img src="img/logo.png" alt="Logo" /></h3></div>
    <div class="control-group">
        <div class="main_input_box">
            <span class="add-on bg_lg"><i class="icon-user"></i></span><input type="text" name="userName" placeholder="{{ $account }}" required="required" />
        </div>
    </div>
    <div class="control-group">
        <div class="main_input_box">
            <span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" name="password" placeholder="{{ $password }}" required="required" />
        </div>
    </div>
    <div class="form-actions">
        {{--<span class="pull-left"><a href="javascript:void(0)" class="flip-link btn btn-info" id="to-recover">{{ $forgotPassword }}</a></span>--}}
        <span class="pull-right"><a href="javascript:void(0)" class="btn btn-success" id="login-submit">{{ $login }}</a></span>
    </div>
</form>

<form id="recover-form" action="#" class="form-vertical recover-form">
    <p class="normal_text">{{ $notice }}</p>
    <div class="controls">
        <div class="main_input_box">
            <span class="add-on bg_lo"><i class="icon-envelope"></i></span><input type="text" name="email" placeholder="{{ $email }}" />
        </div>
    </div>
    <div class="form-actions">
        <span class="pull-left"><a class="flip-link btn btn-success" id="to-login">&laquo; {{ $toLogin }}</a></span>
        <span class="pull-right"><a class="btn btn-info" id="recover-submit">{{ $sendCode }}</a></span>
    </div>
</form>
