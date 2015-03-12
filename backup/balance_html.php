<?php
include "./extensions/order_status.php";
include "./extensions/balance_type.php";
include "./extensions/abs.php";

?><!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title><?php
if ($_obj['balanceType'] == "0"){
?>充值<?php
} else {
?>我的账户<?php
}
?> - <?php
echo $_obj['PHPSayConfig']['sitename'];
?>
</title>
  <link rel="stylesheet" type="text/css" media="screen" href="static/phpsay.css" />
  <script type="text/javascript" src="static/jquery.js"></script>
  <script type="text/javascript" src="static/phpsay.global.js"></script>
  <?php
if ($_obj['balanceType'] == "0"){
?>
  <script type="text/javascript">
  $(document).ready(function(){
    $(".pay-online").click(rechargeOnline);
  });
  </script>
  <?php
}
?>
  <script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?4ae42e8bea25cfe6de700cc26250907d";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</head>
<body>
  <?php
if ($_obj['loginInfo']['uid'] > "0"){
?>
<div class="header web-bg">
  <div class="top-nav">
    <ul class="left-nav">
      <li>
        <a<?php
if ($_obj['headerNavi'] == "home"){
?> class="home"<?php
}
?> title="主页" href="./"><span class="web-icon nav-home"></span>主页</a>
      </li>
      <li>
        <a<?php
if ($_obj['headerNavi'] == "at"){
?> class="home"<?php
}
?> title="提醒" href="./notification.php"><span class="web-icon nav-at"></span>提醒</a>
      </li>
      <li>
        <a<?php
if ($_obj['headerNavi'] == "me"){
?> class="home"<?php
}
?> title="我" href="./user.php"><span class="web-icon nav-me"></span>我</a>
      </li>
    </ul>
    <div class="right-nav">
      <div class="search">
        <form method="get" action="search.php" onsubmit="return searchPHPSay();">
          <input id="search-query" class="search-input" type="text" spellcheck="false" autocomplete="off" name="q" placeholder="搜索">
          <span class="search-icon">
            <button class="web-icon nav-search" type="submit" tabindex="-1"></button>
          </span>
          <input id="search-query-hint" class="search-input search-hinting-input" type="text" spellcheck="false" autocomplete="off" disabled="disabled">
        </form>
      </div>
      <div class="settings">
        <a id="settings"<?php
if ($_obj['headerNavi'] == "settings"){
?> class="home"<?php
}
?> title="设置" href="./settings.php"><span class="web-icon nav-settings"></span></a>
        <div class="dropdown-menu">
          <div class="arrow-top"></div>
          <ul>
            <li><a href="./settings.php">头像</a></li>
            <li><a href="./settings.php?with=email">邮箱</a></li>
            <li><a href="./settings.php?with=password">密码</a></li>
            <li class="logout"><a href="./passport.php?do=logout">退出</a></li>
          </ul>
        </div>
      </div>
      <div class="web-bg write">
        <a class="web-bg write-btn" title="撰写新微文" href="#write"><i class="web-icon nav-write"></i></a>
      </div>
      <script>rightNavBind();</script>
    </div>
  </div>
</div>
<?php
} else {
?>
<div class="header web-bg">
  <div class="top-nav">
    <ul class="left-nav">
      <li><a title="<?php
echo $_obj['PHPSayConfig']['sitename'];
?>
" href="./"><span class="web-icon nav-home"></span><?php
echo $_obj['PHPSayConfig']['sitename'];
?>
</a></li>
    </ul>
    <div class="right-nav">
      <div class="search">
        <form method="get" action="search.php" onsubmit="return searchPHPSay();">
          <input id="search-query" class="search-input" type="text" spellcheck="false" autocomplete="off" name="q" placeholder="搜索">
          <span class="search-icon">
            <button class="web-icon nav-search" type="submit" tabindex="-1"></button>
          </span>
          <input id="search-query-hint" class="search-input search-hinting-input" type="text" spellcheck="false" autocomplete="off" disabled="disabled">
        </form>
      </div>
      <div class="signin">
        <a onclick="goSignIn();"><small>已有账号?</small>登录</a>
      </div>
    </div>
  </div>
</div>
<?php
}
?>
  <div class="container">
    <div class="dashboard ">
      <div class="mini-profile">
  <div class="profile-summary">
    <a href="user.php">
      <div class="profile-content">
        <img src="<?php
echo $_obj['loginInfo']['avatar'];
?>
" id="profile-avatar"><b><?php
echo $_obj['loginInfo']['nickname'];
?>
</b><small>查看我的个人页</small>
      </div>
    </a>
  </div>
  <div class="profile-bottom">
    <script type="text/javascript">showNotifications();</script>
    <a href="javascript:;" onclick="notificationRequest();" class="notify-count"><strong><?php
echo $_obj['notificationNumber'];
?>
</strong>提醒</a>
    <a href="user.php?list=favorite" class="favorite-count"><strong><?php
echo $_obj['favoriteNumber'];
?>
</strong>收藏</a>
    <a href="balance.php" class="balance-count"><strong><?php
echo $_obj['memberBalance'];
?>
</strong>社区币</a>
  </div>
</div>
      <div class="bar-nav">
        <ul class="nav-links">
          <li>
            <a class="first-child<?php
if ($_obj['balanceType'] == ""){
?> active<?php
}
?>" href="balance.php">我的账户<i class="web-icon chev-right"></i></a>
          </li>
          <li>
            <a class="last-child<?php
if ($_obj['balanceType'] == "0"){
?> active<?php
}
?>" href="balance.php?type=recharge">充值<i class="web-icon chev-right"></i></a>
          </li>
        </ul>
      </div>
      <div class="site-footer">
  <div class="footer-inner">
    <div class="footer-copyright">&copy; 2014 PHPSay. Version: <?php
echo $_obj['PHPSayConfig']['version'];
?>
</div>
  </div>
</div>
    </div>
    <?php
if ($_obj['balanceType'] == "0"){
?>
      <div class="content">
        <div class="content-header">
          <div class="header-inner"><h2>充值</h2></div>
        </div>
        <div class="content-footer">
          <div class="content-end-inner">
            <form id="checkout-form" method="post">
              <input type="hidden" name="amount" value="">
              <ul class="content-list">
                <li>充值 <strong>¥1</strong> 获得 <strong>100</strong> 社区币<span class="pay-online" data="1">支付宝支付</span></li>
                <li>充值 <strong>¥2</strong> 获得 <strong>200</strong> 社区币<span class="pay-online" data="2">支付宝支付</span></li>
              </ul>
            </form>
          </div>
        </div>
      </div>
    <?php
}
?>
    <div class="content">
      <div class="content-header">
        <div class="header-inner">
          <h2><?php
if ($_obj['balanceType'] == "0"){
?>充值记录<?php
} else {
?>我的账户<?php
}
?></h2>
        </div>
      </div>
      <ol class="user-items">
        <?php
if ($_obj['balanceType'] == "0"){
?>
          <?php
if (!empty($_obj['resultsList']['list'])){
if (!is_array($_obj['resultsList']['list']))
$_obj['resultsList']['list']=array(array('list'=>$_obj['resultsList']['list']));
$_tmp_arr_keys=array_keys($_obj['resultsList']['list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['resultsList']['list']=array(0=>$_obj['resultsList']['list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['resultsList']['list'] as $rowcnt=>$v) {
if (is_array($v)) $list=$v; else $list=array();
$list['ROWVAL']=$v;
$list['ROWCNT']=$rowcnt;
$list['ROWBIT']=$rowcnt%2;
$_obj=&$list;
?>
            <li class="user-item">
              <div class="stream-content">           
                <div class="stream-item-footer">
                  <?php
echo $_obj['dateline'];
?>

                  <span class="point">•</span>
                  <?php
echo $_obj['platform'];
?>

                  <span class="point">•</span>
                  <?php
echo $_obj['currency'];
?>
 <?php
echo $_obj['amount'];
?>

                  <span class="point">•</span>
                  社区币 <?php
echo $_obj['coin'];
?>

                  <span class="point">•</span>
                  <?php
if ($_obj['status'] == "1"){
?><a href="pay_alipay.php?do=order&id=<?php
echo $_obj['id'];
?>
">立即支付</a><?php
} else {
?><?php
echo _order_status($_obj['status']);
?>
<?php
}
?>
                </div>
              </div>
            </li>
          <?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
        <?php
} else {
?>
          <?php
if (!empty($_obj['resultsList']['list'])){
if (!is_array($_obj['resultsList']['list']))
$_obj['resultsList']['list']=array(array('list'=>$_obj['resultsList']['list']));
$_tmp_arr_keys=array_keys($_obj['resultsList']['list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['resultsList']['list']=array(0=>$_obj['resultsList']['list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['resultsList']['list'] as $rowcnt=>$v) {
if (is_array($v)) $list=$v; else $list=array();
$list['ROWVAL']=$v;
$list['ROWCNT']=$rowcnt;
$list['ROWBIT']=$rowcnt%2;
$_obj=&$list;
?>
            <li class="user-item">
              <div class="stream-content">           
                <div class="stream-item-footer">
                  <?php
echo $_obj['dateline'];
?>

                  <span class="point">•</span>
                  <?php
if ($_obj['tid'] > "0"){
?><a href="t.php?id=<?php
echo $_obj['tid'];
?>
"><?php
echo _balance_type($_obj['typeid']);
?>
</a><?php
} else {
?><?php
echo _balance_type($_obj['typeid']);
?>
<?php
}
?>
                  <span class="point">•</span>
                  <?php
if ($_obj['amount'] > "0"){
?>收入 <?php
echo $_obj['amount'];
?>
<?php
} else {
?>支出 <?php
echo _abs($_obj['amount']);
?>
<?php
}
?>
                  <span class="point">•</span>
                  余额 <?php
echo $_obj['balance'];
?>

                </div>
              </div>
            </li>
          <?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
        <?php
}
?>
      </ol>
      <div class="stream-footer">
        <div class="stream-end-inner">
          <?php
if ($_obj['resultsList']['page']['Total'] > "1"){
?>
            <?php
if ($_obj['resultsList']['page']['Prev'] != ""){
?>
            <a href="<?php
echo $_obj['resultsList']['page']['Prefix'];
?>
<?php
echo $_obj['resultsList']['page']['Prev'];
?>
">上一页</a>
            <?php
if ($_obj['resultsList']['page']['Next'] != ""){
?>
            <span class="pagination"><?php
echo $_obj['resultsList']['page']['Current'];
?>
 / <?php
echo $_obj['resultsList']['page']['Total'];
?>
</span>
            <?php
}
?>
            <?php
}
?>
            <?php
if ($_obj['resultsList']['page']['Next'] != ""){
?>
            <a href="<?php
echo $_obj['resultsList']['page']['Prefix'];
?>
<?php
echo $_obj['resultsList']['page']['Next'];
?>
">下一页</a>
            <?php
}
?>
          <?php
} else {
?>
            <span class="pagination"><?php
if ($_obj['resultsList']['count'] > "0"){
?>已加载全部数据<?php
} else {
?>暂无数据<?php
}
?></span>
          <?php
}
?>
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <div class="alert-messages">
    <div class="message">
      <span class="message-text"></span>
    </div>
  </div>
</body>
</html>