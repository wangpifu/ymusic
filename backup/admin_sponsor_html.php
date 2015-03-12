<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>广告管理 - <?php
echo $_obj['PHPSayConfig']['sitename'];
?>
</title>
  <link rel="stylesheet" type="text/css" media="screen" href="static/phpsay.css" />
  <script type="text/javascript" src="static/jquery.js"></script>
  <script type="text/javascript" src="static/jquery.dragsort.js"></script>
  <script type="text/javascript" src="static/phpsay.global.js"></script>
  <script type="text/javascript" src="static/phpsay.admin.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
    $("#sponsorSetting input").keyup(checkSponsorInput);
    $("#sponsorSetting").submit(buySponsor);
    $(".sponsor-items").dragsort({ dragSelector: "li", dragBetween: true, dragEnd: saveSponsorOrder });
  });
  </script>
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
      <div class="bar-nav">
	<ul class="nav-links">
		<li><a class="first-child<?php
if ($_obj['adminNavi'] == "sponsor"){
?> active<?php
}
?>" href="admin_sponsor.php">链接管理<i class="web-icon chev-right"></i></a></li>
		<li><a<?php
if ($_obj['adminNavi'] == "club"){
?> class="active"<?php
}
?> href="admin_club.php">类目管理<i class="web-icon chev-right"></i></a></li>
		<li><a<?php
if ($_obj['adminNavi'] == "member"){
?> class="active"<?php
}
?> href="admin_member.php">会员管理<i class="web-icon chev-right"></i></a></li>
		<li><a class="last-child<?php
if ($_obj['adminNavi'] == "cache"){
?> active<?php
}
?>" href="admin_cache.php">更新模版缓存<i class="web-icon chev-right"></i></a></li>
	</ul>
</div>
      <div class="sponsor-list">
  <div class="sponsor-inner">
    <div class="sponsor-module-header">
      <h3>链接</h3>
      <?php
if ($_obj['loginInfo']['group'] > "0"){
?>
      <?php
if ($_obj['loginInfo']['group'] > "2"){
?>
        <small> · <a href="admin_sponsor.php">管理</a></small>
      <?php
}
?>
      <?php
}
?>
    </div>
    <ul class="sponsor-items">
      <?php
if (!empty($_obj['sponsorList'])){
if (!is_array($_obj['sponsorList']))
$_obj['sponsorList']=array(array('sponsorList'=>$_obj['sponsorList']));
$_tmp_arr_keys=array_keys($_obj['sponsorList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['sponsorList']=array(0=>$_obj['sponsorList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['sponsorList'] as $rowcnt=>$v) {
if (is_array($v)) $sponsorList=$v; else $sponsorList=array();
$sponsorList['ROWVAL']=$v;
$sponsorList['ROWCNT']=$rowcnt;
$sponsorList['ROWBIT']=$rowcnt%2;
$_obj=&$sponsorList;
?>
      <li data="<?php
echo $_obj['sid'];
?>
">
        <a href="<?php
echo $_obj['link'];
?>
" target="_blank"><?php
echo $_obj['text'];
?>
</a>
      </li>
      <?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
    </ul>
  </div>
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
    <div class="content">
      <form id="sponsorSetting" name="sponsorSetting" method="post">
        <div class="content-header">
          <div class="header-inner"><h2>链接管理</h2></div>
        </div>
        <div class="setting-profile">
          <div class="setting-inner">
            <fieldset class="control-group">
              <label class="control-label" for="linkText">链接文字</label>
              <div class="controls">
                <input type="text" name="linkText" id="linkText" maxlength="32" autocomplete="off">
              </div>
            </fieldset>
            <fieldset class="control-group">
              <label class="control-label" for="linkURL">链接地址</label>
              <div class="controls">
                <input type="text" name="linkURL" id="linkURL" maxlength="255" autocomplete="off">
              </div>
            </fieldset>
          </div>
        </div>
        <div class="setting-footer">
          <div class="setting-end-inner">
            <input type="hidden" name="linkId" value="0" autocomplete="off">
            <button type="submit" class="setting-button" disabled="disabled">保存添加</button>
          </div>
        </div>
      </form>
    </div>
    <div class="content">
      <div class="content-header">
        <div class="header-inner">
          <h2>全部链接</h2>
        </div>
      </div>
      <ol class="user-items">
        <?php
if (!empty($_obj['sponsorAll'])){
if (!is_array($_obj['sponsorAll']))
$_obj['sponsorAll']=array(array('sponsorAll'=>$_obj['sponsorAll']));
$_tmp_arr_keys=array_keys($_obj['sponsorAll']);
if ($_tmp_arr_keys[0]!='0')
$_obj['sponsorAll']=array(0=>$_obj['sponsorAll']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['sponsorAll'] as $rowcnt=>$v) {
if (is_array($v)) $sponsorAll=$v; else $sponsorAll=array();
$sponsorAll['ROWVAL']=$v;
$sponsorAll['ROWCNT']=$rowcnt;
$sponsorAll['ROWBIT']=$rowcnt%2;
$_obj=&$sponsorAll;
?>
          <li class="user-item">
            <div class="stream-content">           
              <div class="stream-item-footer">
                <a href="<?php
echo $_obj['link'];
?>
" target="_blank" id="sponsor-<?php
echo $_obj['sid'];
?>
"><?php
echo $_obj['text'];
?>
</a>
                <span class="point">•</span>
                <?php
if ($_obj['endtime'] == "0"){
?>
                  <a href="javascript:;" onclick="lineSponsor(<?php
echo $_obj['sid'];
?>
,1);">上线</a>
                <?php
} else {
?>
                  <a href="javascript:;" onclick="lineSponsor(<?php
echo $_obj['sid'];
?>
,0);">下线</a>
                <?php
}
?>
                <span class="point">•</span>
                <a href="javascript:;" onclick="editSponsor(<?php
echo $_obj['sid'];
?>
);">修改</a>
                <span class="point">•</span>
                <a href="javascript:;" onclick="deleteSponsor(<?php
echo $_obj['sid'];
?>
);">删除</a>
              </div>
            </div>
          </li>
        <?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
      </ol>
      <div class="stream-footer">
        <div class="stream-end-inner">
          <span class="pagination">共 <?php
echo $_obj['sponsorAllCount'];
?>
 个链接，线上 <?php
echo $_obj['sponsorCount'];
?>
 个链接</span>
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