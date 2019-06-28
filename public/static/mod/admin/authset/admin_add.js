layui.config({
  base: $('#adminadd').data('path')+'/layuiext/' //静态资源所在路径
}).extend({
  formSelects: 'formSelects/formSelects-v4'
}).use(['form', 'formSelects'], function(){
  var form = layui.form
  ,formSelects = layui.formSelects;

  formSelects.btns('roleSel', []);

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
})