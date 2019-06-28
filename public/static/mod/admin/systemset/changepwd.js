layui.use(['form'], function(){
	var form = layui.form
	form.verify({
	  pass: [
	    /^[\S]{6,12}$/
	    ,'密码必须6到12位，且不能出现空格'
	  ]
	  ,repass:function(value){
	  	if(value == ''){
	  		return '请输入确认密码'
	  	}
	  	if(value != $('input[name=password]').val()){
	  		return '两次密码输入不一致'
	  	}
	  }
	})

	form.on('submit(setmypass)', function(obj){
		var url = $(obj.elem).data('url')
		var field = obj.field
		$.post(url, field, function(res){
	      if(res.code == 1){
	      	layer.msg('修改成功，即将退出登录', {
		        icon: 6
		        ,time:2000
		    },function(res){
		        parent.location.replace($(obj.elem).data('reload'));
		    });
	      }else{
	        layer.msg(res.result, {icon: 5});
	      }
	    }, 'json')

	    return false;
	})
})