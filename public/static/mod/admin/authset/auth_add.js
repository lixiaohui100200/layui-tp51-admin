layui.config({
  base: $('#auth_add').data('path')+'/layuiext/' //静态资源所在路径
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

  /** 去掉select选中值空格以及制表符 **/
  form.on('select(fathersel)', function(data){
    var valueStr = data.othis.find('input').val()
    var value = valueStr.replace(/(└)|(\s*)/g, '');
    data.othis.find('input').val(value)
  })

  form.on('radio(runTypeSel)', function(data){
    if(data.value == 2){
      $('#menuCtl').hide();
      $('input[name=is_menu]').prop('checked', false);
      $('#weight').hide();
      $('#iconCtl').hide();
      iconPicker.checkIcon('iconPicker', '');
      $('#logCtl').show();
    }else{
      $('#menuCtl').show();
      $('#logCtl').hide();
      $('input[name=is_log]').prop('checked', false);
    }

    form.render('checkbox');
  })

  form.on('switch(menuSwt)', function(data){
    $('input[name=sorted]').val(0);
    $('input[name=icon]').val('');
    iconPicker.checkIcon('iconPicker', '');
    if(data.elem.checked){
      $('#iconCtl').show();
      $('#weight').show();
    }else{
      $('#iconCtl').hide();
      $('#weight').hide();
    }
  })

  form.verify({
    pidneed: function(value, item){
      if(!$(item).parents('.layui-form-item').is(':hidden') && !value){
        return '请选择该权限所属上级';
      }
    }
    ,Menu: function(value, item){
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
        parent.$('input[name=is_menu]').attr('checked', false);
        parent.$('input[name=title]').val('');
        parent.$('input[name=name]').val('');
        parent.layui.form.render('checkbox')

        parent.layui.table.reload('udzan-rule',{
          where: {is_menu: null, title: null, name : null}
          ,page:{curr:1}
        }); //重载表格
        parent.layer.close(index); //再执行关闭 
      }else{
        layer.msg(res.result, {icon: 5});
      }
    },'json');
  });
})