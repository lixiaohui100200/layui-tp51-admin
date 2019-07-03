layui.config({
  base: $('#roleadd').data('path')+'/layuiext/' //静态资源所在路径
}).extend({
  eleTree: 'eleTree/eleTree'
}).use(['form', 'eleTree'], function(){
  var form = layui.form
  ,eleTree = layui.eleTree

  var tree = eleTree.render({
    elem: '#tree_node'
    ,url: $('#tree_node').data('url')
    ,showCheckbox:true
    ,renderAfterExpand : false
    ,request:{
      name: 'title'
      ,children: 'child'
    }
    ,done:function(res) {
      var rules = res.data
      for (var i = 0; i < rules.length; i++) {
        if(rules[i]['type'] != 3 && !rules[i].hasOwnProperty('child')){
          rules[i]['disabled'] = true
          tree.updateKeySelf(rules[i]['id'], rules[i]);
        }else{
          for (var j = 0; j < rules[i]['child'].length; j++) {
            if(rules[i]['child'][j]['id'] == 6 || rules[i]['child'][j]['id'] == 7) {
              rules[i]['child'][j]['disabled'] = true
              tree.updateKeySelf(rules[i]['child'][j]['id'], rules[i]['child'][j]);
            }
            if (rules[i]['child'][j]['type'] != 3 && !rules[i]['child'][j].hasOwnProperty('child')) {
              rules[i]['child'][j]['disabled'] = true
              tree.updateKeySelf(rules[i]['child'][j]['id'], rules[i]['child'][j]);
            }
          }  
        }
      }
    }
  })

  form.on('checkbox(allcheck)', function(res){
    if(res.elem.checked == true){
      var ruleArr = tree.getAllNodeData()
      var ruleIs = []
      for (var i = 0; i < ruleArr.length; i++) {
        ruleIs[i] = ruleArr[i].id
      }
      tree.setChecked(ruleIs)
    }else{
      tree.unCheckNodes();
    }
  })

  //监听提交
  form.on('submit(role-submit)', function(data){
    var field = data.field; //获取提交的字段
    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    var ruleIs = []

    var ruleArr = tree.getChecked(false, true)
    for (var i = 0; i < ruleArr.length; i++) {
      ruleIs[i] = ruleArr[i].id
    }
    field.rules = ruleIs;
    
    //提交 Ajax 成功后，关闭当前弹层并重载表格
    $.post($('#role-submit').data('url'), data.field, function(res){
      if(res.code == 1){
        parent.layui.table.reload('udzan-role',{page:{curr:1}}); //重载表格
        parent.layer.close(index); 
      }else{
        layer.msg(res.result, {icon: 5});
      }
    },'json');
  });
})