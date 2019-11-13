$(function() {    
    //定时请求刷新  
    setInterval(runajax,60000);   
});  

//ajax方法执行  
function runajax(){  
  // alert();
    $.ajax({
          type: "GET",
          url: "/attendance/getList",
          // data: {username:$("#username").val(), content:$("#content").val()},
          dataType: "json",
          success: function(data){
            // alert(data);
            if(data==true){
              notifyMe('考勤提示','员工提交了新的考勤申请')
            }
          },
          error:function(XMLHttpRequest, textStatus, errorThrown){
            // alert('报错');
          }
    });
}  


function notifyMe(titlePar,bodyPar) {
  var title = titlePar;
  var options = {
      body: bodyPar,
      icon: "http://img.sobot.com/chatres/common/face/admin.png"
  };
 
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    alert("This browser does not support desktop notification");
  }
   
  // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification(title, options);
    notification.onshow = function() {
                setTimeout(function() {
                    notification.close();
                }, 6000000);
            };
  }
 
  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        var notification = new Notification(title, options);
        notification.onshow = function() {
                setTimeout(function() {
                    notification.close();
                }, 6000000);
            };
      }
    });
  }
  // At last, if the user has denied notifications, and you
  // want to be respectful there is no need to bother them any more.
}