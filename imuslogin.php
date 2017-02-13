<html>
<head>
<title>Login Checking System</title>

<link rel="stylesheet" href="//cdn.rawgit.com/TeaMeow/TocasUI/master/dist/tocas.min.css">
<link rel="stylesheet" href="//bootswatch.com/flatly/bootstrap.min.css">
 <script src="//code.jquery.com/jquery-1.9.1.js"></script>

</head>
<body>
  <div class="container">
  <br>
  
<?php
//start session
session_start();

//check if session exists
if(isset($_SESSION["login"])){
	echo '<div class="alert alert-dismissible alert-success" id="session"><strong>成功</strong> 　發現登入記錄，重定向中</div>';
	header("Refresh: 2; url=hello.php"); //set speftic link to main page
}

//if login AJAX was executed , run from here
if(isset($_GET["chk"])){
$ch = curl_init();
$source = 'http://123.203.74.171:8080/imus/api/checkpw.php?username='.$_GET["username"].'&pw='.bin2hex(hash("sha256",md5($_GET["pw"])));
curl_setopt($ch, CURLOPT_URL, $source);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec ($ch);
curl_close ($ch);
$destination = "imus.html";
$file = fopen($destination, "w+");
fputs($file, $data);
fclose($file);
$result =  str_replace("extloginhandler.php","http://imuslab.com/imus/api/extloginhandler.php",file_get_contents("imus.html"));
echo $result;

//set session id
if (strpos($result, 'True') == true) {
$_SESSION["login"] = hash("sha256",md5($_GET["username"]));
}else{ 
session_destroy();
}
//delete cache
unlink("imus.html");
die();

}else{
//if not , show pages
?>

<!--Error -->
<div class="alert alert-dismissible alert-danger" id="pwerr">
<strong>錯誤</strong>  IMUS回傳使用者名稱或密碼錯誤
</div>

<!--Success -->
<div class="alert alert-dismissible alert-success" id="pwok">
<strong>成功</strong> 　請稍候,我們將重新定向你到首頁
</div>

 <br>
 
 
<form id="login" class="form-horizontal" action="#" method="GET">
  <fieldset>
    <legend>使用IMUS系統登入</legend>
    <div class="form-group">
      <label for="username" class="col-lg-2 control-label">使用者名稱</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" id="username" placeholder="使用者名稱">
      </div>
    </div>
    <div class="form-group">
      <label for="password" class="col-lg-2 control-label">密碼</label>
      <div class="col-lg-10">
        <input type="password" class="form-control" id="password" placeholder="密碼">
      </div>
    </div>
    <div class="form-group">
      <div class="col-lg-10 col-lg-offset-2">
        <button type="reset" class="btn btn-default">重設</button>
        <button type="submit" class="btn btn-primary" id="submit">送出</button>
      </div>
    </div>

  </fieldset>
</form>

</div>
<script type='text/javascript'>
<!--Set Alert Disable on Default -->
$(document).ready(function(){
	 $("#pwerr").hide();
		$("#pwok").hide();
});

<!--AJAX post Data to imus -->
	$("#submit").click(function(){
		$("#pwerr").hide();
		$("#pwok").hide();
		$("#session").hide();
        $.get("#", { username: $('#username').val(), pw: $('#password').val() , chk : "true" }, function(data, status){
			<!--if return true-->
			if (data.replace("<body>", "").replace("<head>", "").replace("</head>", "").replace("</body>", "").replace("<html>", "").replace("</html>", "").replace(/^\s*[\r\n]/gm, "").indexOf("True") >= 0) 
			{ $("#pwok").show(1000);setTimeout(function(){
				<!--set speftic link to main page -->
				window.location.replace("hello.php");
				}, 2000);}
				<!--else action -->
			else {  $("#pwerr").show(1000); }
	   });
    });
</script>

</body>
</html>
<?php } ?>