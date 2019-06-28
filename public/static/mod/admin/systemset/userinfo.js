layui.use(['form'], function(){
	var form = layui.form

	form.verify({
    adminPhone:function(value, item){
      if(!value && !$('input[name="email"]').val()){
        return '手机号和邮箱必填一项';
      }
      if(value && ! /^1[356789]{1}\d{9}$/.test(value)){
        return '手机号格式不正确';
      }
    },
    adminEmail:function(value, item) {
      if(!value && !$('input[name="phone"]').val()){
        return '手机号和邮箱必填一项';
      }
      if(value && ! /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)){
        return '邮箱格式不正确';
      }
    }
  })

	form.on('submit(setmyinfo)', function(obj){
		var url = $(obj.elem).data('url')
		var field = obj.field
		$.post(url, field, function(res){
      if(res.code == 1){
      	layer.msg('修改成功', {icon: 1}, function(){
      		window.location.reload()	
      	})
      }else{
        layer.msg(res.result, {icon: 5});
      }
    }, 'json')

    return false;
	})
});