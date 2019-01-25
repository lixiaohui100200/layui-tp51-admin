$(function(){
	$('#listenPhone').on('input blur', function(e){
		var obj = $(this)
		var phone = obj.val()
		$('.error-tip').html('')
		$('.show-tip').html('')
		if(phone.length == 11){
			if(e.type == 'blur'){
				return false;
			}
			$.post(obj.data('url'), {phone:phone}, function(res){
				res = $.parseJSON(res);
				if(res.code == 100){
					$('#username').val(res.result.name)
					$('.error-tip').html('')
					if(typeof res.result == 'string') $('.show-tip').html(res.result)
				}else{
					$('#username').val('')
					$('.error-tip').html(res.result)
				}
			})
		}else if(phone.length > 11){
			$('.error-tip').html('手机号不能超过11位')
		}else{
			if(e.type == 'blur'){
				$('.error-tip').html('请输入正确的手机号')
			}
		}
	})

	var dialog = YDUI.dialog;
	var util = YDUI.util;
	var keyboard = $('#J_KeyBoard');
	var getCode = $('#J_GetCode');

  	// 初始化安全键盘
    keyboard.keyBoard({
        disorder: false, // 是否打乱数字顺序
        title: '微金安全键盘' // 显示标题
    });
    $('.keyboard-head').html('<strong>输入动态密码</strong>');

    // 初始化短信发送
    getCode.sendCode({
        disClass: 'btn-disabled', // 禁用按钮样式【必填】
        secs: 60, // 倒计时时长 [可选，默认：60秒]
        run: false,// 是否初始化自动运行 [可选，默认：false]
        runStr: '{%s}秒后重新获取',// 倒计时显示文本 [可选，默认：58秒后重新获取]
        resetStr: '重新获取验证码'// 倒计时结束后按钮显示文本 [可选，默认：重新获取验证码]
    });

    getCode.on('click', function () {
        var _this = $(this);
        var phone = $('#listenPhone').val()
        dialog.loading.open('发送中...');
        $.post(getCode.data('url'), {phone:phone},function(res){
			dialog.loading.close();
	        _this.sendCode('start');
	        dialog.toast('已发送', 'success', 1000, function(){
	        	keyboard.keyBoard('open');
	        });
		})
    });


	$('#firstlogin').click(function(){
		if(util.localStorage.get('islogged') == true){
			dialog.notify('您已登录', 1000, function(){
				util.localStorage.set('islogged', false);
			    window.location.replace(keyboard.data('reload'))
			});
			return false;
		}

		var obj = $(this)
		if($('#listenPhone').val() == "" || $('.error-tip').html() != "" || $('#username').val() == ""){
			dialog.notify('请完善您的信息', 1000);
			return false;
		}

		var phone = $('#listenPhone').val()
		var phonestr = phone.substr(-4)
		var name = $('#username').val()
		
		dialog.confirm('即将发送验证码', '确定使用尾号'+phonestr+'的手机号？', [{
            txt: '取消',
            color: false, 
            callback: function () {}
        },
        {
            txt: '确定',
            color: '#0082ff',
            callback: function () {
                dialog.loading.open('发送中...');
		        $.post(getCode.data('url'), {phone:phone},function(res){
					dialog.loading.close();
			        getCode.sendCode('start');
			        dialog.toast('已发送', 'success', 1000, function(){
			        	res = $.parseJSON(res);
			        	if(res.code == 100){
			        		keyboard.keyBoard('open');
			        	}
			        });
				})
            }
        }]);
	})

	// 六位密码输入完毕后执行
    keyboard.on('done.ydui.keyboard', function (ret) {
    	var phone = $('#listenPhone').val()
    	var name = $('#username').val()
    	var sixcode = ret.password
        console.log('输入的密码是：' + ret.password);

        // 弹出请求中提示框
        dialog.loading.open('正在验证...');

        $.post(keyboard.data('url'),{phone:phone,name:name,sixcode:sixcode},function(res){
        	dialog.loading.close();
        	res = $.parseJSON(res);
        	if(res.code == 100){
        		//标记已登录
        		util.localStorage.set('islogged', true);
        		// 关闭请求中提示框
        		dialog.loading.close();
        		//成功提示
        		dialog.toast('登录成功', 'success', 1500);
        		setTimeout(function () {
        			// 关闭键盘
        			keyboard.keyBoard('close');
	                window.location.replace(keyboard.data('reload'))
	            }, 1000);
				return true;
        	}else{
        		keyboard.keyBoard('error', res.result);	
        	}
        });
    });
})