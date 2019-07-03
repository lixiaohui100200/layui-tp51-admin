layui.use(['table'], function(){
  var $ = layui.$
  ,form = layui.form
  ,table = layui.table;

  var tableInx =table.render({
    elem: '#udzan-admin'
    ,url: $('#udzan-admin').data('url') //数据接口
    ,page: true //开启分页
    ,limits:[10,15,20]
    ,done:function (res, curr, count) {
      if(count <= this.limit){
        $('.layui-table-page').addClass('layui-hide');//总条数小于每页限制条数时隐藏分页
      }
    }
    ,cols: [[ //表头
      {field: 'id', title: 'ID',  sort: true, width:65}
      ,{field: 'login_name', title: '用户名'}
      ,{field: 'name', title: '姓名'}
      ,{field: 'phone', title: '手机号'} 
      ,{field: 'email', title: '邮箱'}
      ,{field: 'groupNames', title: '角色'}
      ,{field: 'create_time', title: '创建时间', sort: true}
      ,{field: 'status', title: '状态', templet: '#statusTpl'}
      ,{title: '操作', toolbar: '#optTpl'}
    ]]
  });
  
  //监听搜索
  form.on('submit(admin-search)', function(data){
    var field = data.field;
    
    //执行重载
    table.reload('udzan-admin', {
      where: field
      ,page:{curr:1}
    });
  });

  table.on('sort(udzan-admin)',function(obj){
    if(tableInx.config.page.count <= tableInx.config.page.limit){
      $('.layui-table-page').addClass('layui-hide');//总条数小于每页限制条数时隐藏分页
    }
  })

  form.on('checkbox(optstatus)', function(obj){
    var wish = obj.elem.checked
    var id = obj.value
    var name = $(obj.elem).data('name')
    $.post($('#udzan-admin').data('sturl'),{id:id, status: wish, name: name}, function(res){
      if(res.code != 1){
        layer.tips(res.result, obj.othis, {tips:1});
        obj.elem.checked = !wish;
        form.render('checkbox');
      }
    }, 'json')
  })

  //事件
  var active = {
    add: function(){
      layer.open({
        type: 2
        ,title: '添加管理员'
        ,content: $('#addOpt').data('url')
        ,area: ['420px', '500px']
        ,btn: ['确定', '取消']
        ,yes: function(index, layero){
          var iframeWindow = window['layui-layer-iframe'+ index]
          ,submitID = 'asuma-admin-submit'
          ,submit = layero.find('iframe').contents().find('#'+ submitID);

          //监听提交
          iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
            var field = data.field; //获取提交的字段
            
            //提交 Ajax 成功后，静态更新表格中的数据
            $.post($('#optInsert').data('url'), field, function(res){
              if(res.code == 1){
                table.reload('udzan-admin',{page:{curr:1}}); //数据刷新
                layer.close(index); //关闭弹层
              }else{
                layer.msg(res.result, {icon: 5});
              }
            }, 'json')
            
          });
          
          submit.trigger('click');
        }
      }); 
    }
    ,edit:function(obj){
      layer.open({
        type: 2
        ,title: '编辑管理员'
        ,content: $('#addOpt').data('url')+"?id="+obj.data.id
        ,maxmin: true
        ,btnAlign:'l'
        ,area: ['420px', '550px']
        ,btn: ['确定', '取消']
        ,yes: function(index, layero){
          var iframeWindow = window['layui-layer-iframe'+ index]
          ,submitID = 'asuma-admin-submit'
          ,submit = layero.find('iframe').contents().find('#'+ submitID);

          //监听提交
          iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
            var field = data.field; //获取提交的字段
            
            //提交 Ajax 成功后，静态更新表格中的数据
            $.post($('#optUpdate').data('url'), field, function(res){
              if(res.code == 1){
                table.reload('udzan-admin'); //数据刷新
                layer.close(index); //关闭弹层
              }else{
                layer.msg(res.result, {icon: 5});
              }
            }, 'json')
            
          });
          submit.trigger('click');
        }
      });
    }
    ,del:function(obj) {
      let msg = '即将删除用户：'+obj.data.login_name
      layer.confirm(msg, {title:'<span style="color:red;font-weight:bold;font-size:15px;">确认删除？</span>'},function(index){
        $.post($('#udzan-admin').data('sturl'), {id:obj.data.id, status: 'delete',name: obj.data.login_name}, function(res){
          if(res.code == 1){
            layer.close(index)
            tableInx.reload()
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
  }

  table.on('tool(udzan-admin)', function(obj){
    var type = obj.event
    active[type].call(this, obj);
  })

  $('.layui-btn.layuiadmin-btn-admin').on('click', function(){
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });
});