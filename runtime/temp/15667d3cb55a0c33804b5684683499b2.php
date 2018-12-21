<?php /*a:2:{s:65:"E:\WWW\weijin\zlian_i\application\index\view\ding\firstlogin.html";i:1545354637;s:61:"E:\WWW\weijin\zlian_i\application\index\view\public\base.html";i:1545210069;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<title>首次登录验证</title>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0,uc-fitscreen=yes" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no" />
	
	<link rel="stylesheet" href="/static/css/ding/firstlogin.css">

	<script type="text/javascript" src="/static/js/public/jquery-3.3.1.min.js"></script>
	
</head>
<body>
	
	<div class="scan-apply">
	    <!-- <img src="/static/images/ding/wjshop.png" alt="" /> -->
	    <!-- <h3>需先验证您在<b style="color: #0082ff;">微金小店</b>注册的手机号</h3> -->
	    <form id="loginform">
	        <label class="phone">
	            <input type="number" value="" name="phone" placeholder="请输入手机号" pattern="[0-9]" id="listenPhone" data-url="<?php echo url('ding/checkPhone'); ?>"/>
	        </label>
	       	<label class="name">
	            <input type="text" name="name" value="" readonly="readonly" id="username"/>
	        </label>
	        <div class="yzm">
	            <label class="yzm-code">
	                <input type="number" value="" name="verify" placeholder="验证码" pattern="[0-9]"/>
	            </label>
	            <!-- <input  type="button" value="获取验证码" onClick="sendMessage()" class="btn btn-success"/> -->
	            <button id="btnSendCode" class="btn btn-success" >获取验证码</button>
	        </div>
	    </form>
	    <span class="error-tip"></span>
	    <button type="button" class="btn btn-success" id="firstlogin" data-url="<?php echo url('markLinkLogin'); ?>">确定</button>
	    <p class="btn btn-cancel" >取消</p>
	</div>

	
	<!-- <script src="__PUBLIC__/lib/layer/mobile/layer.js" type="text/javascript" charset="utf-8"></script> -->
	<!-- <script src="/static/js/ding/yzm.js" type="text/javascript" charset="utf-8"></script> -->
	<script src="/static/js/ding/firstlogin.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>