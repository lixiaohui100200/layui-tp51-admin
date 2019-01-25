$.ajaxSetup({ 
    complete:function(XMLHttpRequest, textStatus){
        if("redirect" == XMLHttpRequest.getResponseHeader("Ajax-Mark")){ 
            //从后端响应header中判断是否要跳转
            var win = window;
            while(win != win.top){
                win = win.top;
            }
            win.location.replace(XMLHttpRequest.getResponseHeader("Redirect-Path"));
        }
    }
});