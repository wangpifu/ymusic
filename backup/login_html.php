<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title><?php
echo $_obj['PHPSayConfig']['sitename'];
?>
 - 微社区</title>
	<meta name="description" content="微社区，轻交流！"/>
	<meta name="keywords" content="微社区, <?php
echo $_obj['PHPSayConfig']['sitename'];
?>
, 登录, 注册" />
	<link rel="stylesheet" type="text/css" media="screen" href="static/login.css?20" />
	<script type="text/javascript" src="static/jquery.js"></script>
	<script type="text/javascript" src="static/jquery.form.js"></script>
	<script type="text/javascript" src="static/phpsay.login.js"></script>
	<script type="text/javascript">
	var connect = "<?php
echo $_obj['connectArray']['connect'];
?>
";
	</script>
	<script>

</script>
</head>
<body>
	<div class="header"><?php
echo $_obj['PHPSayConfig']['sitename'];
?>
</div>
	<?php
if ($_obj['connectArray']['connect'] == ""){
?>
	<div class="loginwindow hidden" id="loginSelect">
		<div class="logintype">
			<div class="qqlogin">
				<div class="qqicon"></div>QQ帐号登录
			</div>
			<div class="emaillogin">
				<div class="emailicon"></div>使用邮箱登录
			</div>
		</div>
		<div class="logintips">
			微社区，轻交流！PHPSay全新启航
		</div>
	</div>
	<div class="connectwindow hidden" id="loginView">
		<div class="closewindow" id="closesignin"></div>
		<div class="title">登录 PHPSay</div>
		<form id="loginform">
			<div class="connectform">
				<div class="connectinput">
					<div class="placeholder">邮箱或昵称</div>
					<input class="ipt iptlong" type="text" maxlength="36" spellcheck="false" value="" name="account" autocomplete="off">
				</div>
				<div class="connectinput" id="login-control">
					<div class="placeholder">密码</div>
					<input class="ipt" type="password" maxlength="26" spellcheck="false" value="" name="password" autocomplete="off">
					<input type="submit" value="" class="btn">
				</div>
			</div>
		</form>
		<div class="connecttip" id="logintip">
			<a href="./passport.php?do=password" class="forgot">如果您忘记了密码，请点击这里找回</a>
			<?php
if (!empty($_obj['PHPSayConfig']['emailjoin'])){
?>
			<a href="javascript:;" onclick="showSignupView();" class="signup">如果您还不是我们的会员，请点此注册</a>
			<?php
} else {
?>
			<a href="javascript:;" onclick="location.href='./connect.php?do=QQLogin';" class="signup">您也可以使用QQ帐号登录微社区</a>
			<?php
}
?>
		</div>
	</div>
	<div class="connectwindow hidden" id="signupView">
		<div class="closewindow" id="closesignup"></div>
		<div class="titlelow">加入 PHPSay</div>
		<form id="joinform">
			<div class="connectform">
				<div class="connectinput">
					<div class="placeholder">邮箱</div>
					<input class="ipt iptlong" type="text" maxlength="36" spellcheck="false" value="" name="email" autocomplete="off">
				</div>
				<div class="connectinput">
					<div class="placeholder">昵称</div>
					<input class="ipt iptlong" type="text" maxlength="13" spellcheck="false" value="" name="nickname" autocomplete="off">
				</div>				
				<div class="connectinput" id="signup-control">
					<div class="placeholder">密码</div>
					<input class="ipt" type="password" maxlength="26" spellcheck="false" value="" name="password" autocomplete="off">
					<input type="submit" value="" class="btn">
				</div>
			</div>
		</form>
	</div>
	<?php
} else {
?>
	<div class="connectwindow">
		<div class="connectavatar">
			<img src="<?php
if ($_obj['connectArray']['avatar'] != ""){
?><?php
echo $_obj['connectArray']['avatar'];
?>
<?php
} else {
?>static/avatar.jpg<?php
}
?>">
		</div>
		<div class="connectinfo">
			<?php
if ($_obj['connectArray']['connect'] == "QQ"){
?>
			您已成功使用QQ互联功能连接！
			<?php
}
?>
		</div>
		<form id="connectform">
			<div class="connectform">
				<div class="connectinput topborder" id="connect-control">
					<input class="ipt" type="text" maxlength="13" spellcheck="false" value="<?php
echo $_obj['connectArray']['nickname'];
?>
" name="nickname" placeholder="昵称" autocomplete="off">
					<input type="submit" value="" class="btn">
				</div>
			</div>
		</form>
		<div class="connecttip">
			请设定您的昵称，此项以后不可更改
		</div>
	</div>
	<?php
}
?>
	<div class="footer">
		<div class="links">
			<a href="http://www.miibeian.gov.cn/" target="blank">苏ICP备13009740号-3</a>
			<span class="separator"></span>
			Copyright &copy; 2006~2014 PHPSay World. 保留所有权利。
		</div>
	</div>
</body>
</html>