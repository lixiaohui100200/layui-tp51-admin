layui.use(['table'], function(){
  var table = layui.table
  ,form = layui.form;

  var tableIns = table.render({
    elem: '#udzan-rule'
    ,url:  $('#udzan-rule').data('url')
    ,page: true //开启分页
    ,limit:20
    ,limits:[15,20,30,50]
    ,done:function (res, curr, count) {
      if(count <= this.limit){
        $('.layui-table-page').addClass('layui-hide');//总条数小于每页限制条数时隐藏分页
      }
    }
    ,cols: [[ //表头
    	{field: 'title', title: '名称', minWidth : '140', templet: function(d){
        var str = '';
        switch(d.level){
          case 1: 
            str = '<span style="padding:0 5px 0 8px;">└</span>';
            break;
          case 2: 
            str = '<span style="padding:0 5px 0 25px;">└</span>';
            break;
        }
        return str+d.title;
      }}
    	,{field: 'type', title: '权限类型', templet:function(d){
        let typeStr = ''
        switch(d.type){
          case 1: typeStr = '模块'; break;
          case 2: typeStr = '子模块'; break;
          case 3: typeStr = '节点'; break;
        }

        return typeStr;
      }}
      ,{field: 'name', title: '标识'}
      ,{field: 'run_type', title: '表现类型', templet: function(d){
        var run_type = ''
        switch(d.run_type){
          case 1:  run_type = '普通' ; break;
          case 2:  run_type = '异步'; break;
        }
        return run_type;
      }}
      ,{field: 'is_menu', title: '是否菜单', templet: function(d){
        if(d.is_menu){
          return '<button class="layui-btn layui-btn-xs layui-btn-normal">是</button>'
        }else{
          return '<button class="layui-btn layui-btn-xs layui-btn-primary">否</button>'
        }
      }}
      ,{field: 'icon', title: '图标', templet: function(d){
        if(d.icon){
          return '<i class="layui-icon '+d.icon+'"></i>'
        }else{
          return '';
        }
      }}
      ,{field: 'sorted', title: '权重', edit : 'text'}
      ,{field: 'is_logged', title: '记录日志', templet:"#loggedTpl"}
      ,{field: 'status', title: '状态', templet: '#statusTpl'}
      ,{title: '操作', toolbar: '#optTpl'}
    ]]
  });

  form.on('checkbox(chooseMenu)', function(data){
    let is_menu = 0;
    if(data.elem.checked == true){
      is_menu = 1
    }
    $('input[name="title"]').val('');
    $('input[name="name"]').val('');
    //执行重载
    tableIns.reload({
      where: {is_menu: is_menu, title: null, name : null}
      ,page:{curr:1}
    });
  })
  
  //监听搜索
  form.on('submit(listSearch)', function(data){
    let field = data.field;
    
    //执行重载
    tableIns.reload({
      where: field
      ,page:{curr:1}
    });

    return false;
  });

  //日志开关
  form.on('switch(logSwitch)', function(obj){
    var wish = obj.elem.checked

    $.post($('#loggedTpl').data('url'),{id: obj.value, is_logged: wish}, function(res){
      if(res.code != 1){
        layer.tips(res.result, obj.othis, {tips:1});
        obj.elem.checked = !wish;
        form.render('checkbox');
      }
    },'json')
  })

  form.on('checkbox(optstatus)', function(obj){
    var wish = obj.elem.checked
    var id = obj.value
    $.post($('#statusTpl').data('url'),{id:id, status: wish}, function(res){
      if(res.code != 1){
        layer.tips(res.result, obj.othis, {tips:1});
        obj.elem.checked = !wish;
        form.render('checkbox');
      }
    }, 'json')
  })

  table.on('edit(udzan-rule)', function(obj){
    var is_menu = obj.data.is_menu
    if(is_menu != 1){
      var objVal = $(this).prev().text();
      layer.tips('非菜单无法设置权重', obj.tr.find('td:eq(6)'), {tips:1});
      setTimeout(function(){obj.update({sorted : objVal})})
      return false;
    }

    if(!/^(0|[1-9]+[0-9]*)$/.test(obj.value)){
      layer.tips('请输入正确的数值', obj.tr.find('td:eq(6)'), {tips:1});
      setTimeout(function(){obj.update({sorted : objVal})})
      return false;  
    }

    $.post($('#udzan-rule').data('sturl'),{id: obj.data.id, newVal: obj.value, is_menu : is_menu}, function(res){
      if(res.code != 1){
        layer.tips(res.result, obj.tr.find('td:eq(6)'), {tips:1});
        obj.update({sorted : objVal})
      }else{
        tableIns.reload()
      }
    }, 'json')
  })

  table.on('tool(udzan-rule)', function(obj){
    if(obj.event == 'del'){
      active['del'].call(this, obj);
    }else if(obj.event == 'edit'){
      active['edit'].call(this, obj);
    }
  })
  
  var active = {
    add: function(){
      layer.open({
        type: 2
        ,title: '添加权限'
        ,content: $('#authOpt').data('add')
        ,maxmin: true
        ,area: ['820px', '600px']
        ,btn: ['确定', '取消']
        ,yes: function(index, layero){
          //点击确认触发 iframe 内容中的按钮提交
          var submit = layero.find('iframe').contents().find("#auth-submit");
          submit.click();
        }
      });
    }
    ,edit: function(obj){
      layer.open({
        type: 2
        ,title: '编辑权限'
        ,content: $('#authOpt').data('edit')+"?rule="+obj.data.id
        ,maxmin: true
        ,area: ['820px', '560px']
        ,btn: ['确定', '取消']
        ,success: function(layero, index){
          layero.find('input[name=rule_id]').val(obj.data.id)
        }
        ,yes: function(index, layero){
          //点击确认触发 iframe 内容中的按钮提交
          var submit = layero.find('iframe').contents().find("#auth-submit");
          submit.click();
        }
      });
    }
    ,del: function(obj){
      layer.prompt({
        title: '<span style="color:red;font-weight:bold;font-size:15px;">删除后不可恢复</span>'
        ,formType: 1
        ,success:function(layero){
          layero.find('input').attr('placeholder', '请输入密码')
      }},function(value, index, elem){
        $.post($('#statusTpl').data('url'),{id:obj.data.id, status: 'delete', password: value}, function(res){
          if(res.code == 1){
            layer.close(index)
            tableIns.reload()
          }else if(res.code == -2){
            layer.msg(res.result, {time:1500},function(){
              layer.close(index)
            });
          }else{
            layer.msg(res.result);
          }
        }, 'json')
      });
    }
  };

  $('.layui-btn.layuiadmin-btn-list').on('click', function(){
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });

});