layui.use(['table'], function(){
  var $ = layui.$
  ,form = layui.form
  ,table = layui.table;

  var tableIns = table.render({
    elem: '#udzan-role'
    ,url: $('#udzan-role').data('url')
    ,page: true //开启分页
    ,limit: 15
    ,limits:[10,15,20]
    ,done:function (res, curr, count) {
      if(count <= this.limit){
        $('.layui-table-page').addClass('layui-hide');//总条数小于每页限制条数时隐藏分页
      }
    }
    ,cols: [[ //表头
      {field: 'id', title: 'ID', width:65}
      ,{field: 'title', title: '角色名'}
      ,{field: 'remark', title: '描述'}
      ,{field: 'status', title: '状态', templet: '#statusTpl'}
      ,{title: '操作', toolbar: '#optTpl'}
    ]]
  });
  
  //搜索角色
  form.on('select(LAY-user-adminrole-type)', function(data){
    //执行重载
    table.reload('LAY-user-back-role', {
      where: {
        role: data.value
      }
    });
  });

  //事件
  var active = {
    add: function(){
      layer.open({
        type: 2
        ,title: '添加新角色'
        ,content: $('#addOpt').data('url')
        ,area: ['600px', '550px']
        ,btn: ['确定', '取消']
        ,yes: function(index, layero){
          var submit = layero.find('iframe').contents().find("#role-submit");
          submit.trigger('click');
        }
      }); 
    }
    ,edit:function(obj){
      layer.open({
        type: 2
        ,title: '编辑角色'
        ,content: $('#addOpt').data('url')+"?id="+obj.data.id
        ,maxmin: true
        ,btnAlign:'l'
        ,area: ['600px', '550px']
        ,btn: ['确定', '取消']
        ,yes: function(index, layero){
          var submit = layero.find('iframe').contents().find("#role-submit");
          submit.trigger('click');
        }
      });
    }
    ,del: function(obj){
      layer.prompt({
        title: '<span style="color:red;font-weight:bold;font-size:15px;">确认删除？</span>'
        ,formType: 1
        ,success:function(layero){
          layero.find('input').attr('placeholder', '请输入密码')
      }},function(value, index, elem){
        $.post($('#udzan-role').data('sturl'),{id:obj.data.id, status: 'delete', password: value, name:obj.data.title}, function(res){
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
  }

  form.on('checkbox(optstatus)', function(obj){
    var wish = obj.elem.checked
    var id = obj.value
    var name = $(obj.elem).data('name')
    $.post($('#udzan-role').data('sturl'),{id:id, status: wish,name: name}, function(res){
      if(res.code != 1){
        layer.tips(res.result, obj.othis, {tips:1});
        obj.elem.checked = !wish;
        form.render('checkbox');
      }
    }, 'json')
  })

  table.on('tool(udzan-role)', function(obj){
    if(obj.event == 'del'){
      active['del'].call(this, obj);
    }else if(obj.event == 'edit'){
      active['edit'].call(this, obj);
    }
  })

  $('.layui-btn.layuiadmin-btn-role').on('click', function(){
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });
});