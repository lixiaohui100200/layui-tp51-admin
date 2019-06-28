layui.use(['table', 'laydate', 'form'], function(){
  var table = layui.table
  ,form = layui.form
  ,laydate = layui.laydate

  laydate.render({
    elem: '#timepicker'
    ,range: '~'
    ,theme: 'asuma'
  });

  form.on('submit(log-search)', function(data){
    var field = data.field;
    
    //执行重载
    table.reload('operation-log', {
      where: field
      ,page:{curr:1}
    });
  });

  table.render({
    elem: '#operation-log'
    ,url: $('#operation-log').data('url') //数据接口
    ,page: true //开启分页
    ,limits:[10,15,20]
    ,done:function (res, curr, count) {
      if(count <= this.limit){
        $('.layui-table-page').addClass('layui-hide');//总条数小于每页限制条数时隐藏分页
      }
    }
    ,cols: [[ //表头
      {type:'checkbox'}
      ,{field: 'behavior_user', title: '行为用户'}
      ,{field: 'auth_title', title: '行为名称'}
      ,{field: 'auth_desc', title: '行为描述'}
      ,{field: 'auth_name', title: '行为标识'} 
      ,{field: 'ip', title: 'IP'}
      ,{field: 'record_time', title: '记录时间'}
    ]]
  });

  var active = {
    batchDel: function(obj){
      var checkStatus = table.checkStatus('operation-log')
      ,data = checkStatus.data;
      var idArr = []
      $.each(data, function(i, v){
        idArr.push(v.id)
      })

      var ids = idArr.join(',')

      if(ids == ''){
        layer.msg('请选择要删除的数据')
        return false;
      }

      layer.prompt({
        title: '<span style="color:red;font-weight:bold;font-size:15px;">删除后不可恢复</span>'
        ,formType: 1
        ,success:function(layero){
          layero.find('input').attr('placeholder', '请输入密码')
      }},function(value, index, elem){
        $.post(obj.data('url'), {ids: ids, password: value}, function(res){
          if(res.code == 1){
            layer.msg('删除成功');
            layer.close(index)
            table.reload('operation-log')
          }else{
            layer.msg(res.result);
          }
        }, 'json')
      });

      
    }
  }

  $('.layui-btn.layuiadmin-btn-admin').on('click', function(){
    var type = $(this).data('type');
    active[type] ? active[type].call(this, $(this)) : '';
  });
})