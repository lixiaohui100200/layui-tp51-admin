layui.config({
  base: $('#auth_edit').data('path')+'/layuiext/' //静态资源所在路径
}).use(['form', 'laytpl', 'iconPicker'], function(){
  var form = layui.form 
  ,laytpl = layui.laytpl
  ,iconPicker = layui.iconPicker;

  iconPicker.render({
      // 选择器，推荐使用input
      elem: '#iconPicker',
      // 数据类型：fontClass/unicode，推荐使用fontClass
      type: 'fontClass',
      // 是否开启搜索：true/false，默认true
      search: false,
      // 是否开启分页：true/false，默认true
      page: true,
      // 每页显示数量，默认12
      limit: 20,
      cellWidth: '20%',
      // 点击回调
      click: function (data) {
          //console.log(data);
      },
      // 渲染成功后的回调
      success: function(d) {
          //console.log(d);
      }
  });

  form.on('switch(menuSwt)', function(data){
    if(data.elem.checked){
      $('#iconCtl').show();
      $('#weight').show();
    }else{
      $('#iconCtl').hide();
      $('#weight').hide();
    }
  })

  form.on('select(typeSel)', function(data){
    if(data.value != "" && data.value != 1){
      $('#fatherMod').show();
    }else{
      $('#fatherMod').hide();
      $('.fathersel').html('<option></option>');
      form.render('select');
      return false;
    }
    
    $.post($(data.elem).data('url'), {type:data.value}, function(data){
      let getTpl = $('#treeMods').html()
      laytpl(getTpl).render(data, function(html){
        $('.fathersel').html(html);
        form.render('select');

        /** 修改select内部样式 **/
        $('.fathersel').next().children('dl').children('dd').each(function(i, e) {
          let str = $(e).html()
          let arr = str.split('(');
          if(arr.length == 2){
            arr[1] = '<span style="color:#D2D2D2;">(' + arr[1] + '</span>';
            $(e).html(arr.join(''))
          }
          if($('.fathersel>option:eq('+i+')').data('l') == 2){
            $(e).css('padding-left', '20px');
          }
        })
      });
    })
  })

  //触发 form select事件，默认选中权限所属上级
  $('select[name=type]').next('.layui-form-select').children('dl').children('dd.layui-this').click();
  //禁止选中权限类型
  $('select[name=type]').attr('disabled',true)
  form.render('select');

  form.verify({
    Menu: function(value, item){
      if($('input[name=is_menu]').prop('checked') && !$.trim(value)){
        return '请给菜单选择一个图标';
      }
    }
    ,descNeed: function(value, item){
      if($('input[name=is_log]').prop('checked') && !$.trim(value)){
        return '开启记录日志时权限描述为必填项';
      }
    }
  })

  //监听提交
  form.on('submit(auth-submit)', function(data){
    var field = data.field; //获取提交的字段
    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引  

    //提交 Ajax 成功后，关闭当前弹层并重载表格
    $.post($(data.elem).data('url'), data.field, function(res){
      if(res.code == 1){
        parent.layui.table.reload('udzan-rule'); //重载表格
        parent.layer.close(index); //再执行关闭 
      }else{
        layer.msg(res.result, {icon: 5});
      }
    },'json');
  });
})