<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title><?php
if ($_obj['clubList']['current']['cid'] > "0"){
?><?php
echo $_obj['clubList']['current']['cname'];
?>
 - <?php
}
?><?php
echo $_obj['PHPSayConfig']['sitename'];
?>
</title>
  <link rel="stylesheet" type="text/css" media="screen" href="static/phpsay.css?v1" />
  <link rel="stylesheet" type="text/css" media="screen" href="static/uploadify.css?v1" />
  <script type="text/javascript" src="static/jquery.js"></script>
  <script type="text/javascript" src="static/jquery.uploadify.min.js"></script>
  <script type="text/javascript" src="static/jquery.image.js"></script>
  <script type="text/javascript" src="static/phpsay.global.js"></script>
  <script type="text/javascript" src="static/phpsay.upload.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
    indexPageOnload(<?php
echo $_obj['loginInfo']['uid'];
?>
);
  });
  </script>
  <script>

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
      <?php
if ($_obj['loginInfo']['uid'] > "0"){
?>
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
      <?php
}
?>
      <div class="bar-nav">
  <ul class="nav-links">
    <?php
if (!empty($_obj['clubList']['list'])){
if (!is_array($_obj['clubList']['list']))
$_obj['clubList']['list']=array(array('list'=>$_obj['clubList']['list']));
$_tmp_arr_keys=array_keys($_obj['clubList']['list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['clubList']['list']=array(0=>$_obj['clubList']['list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['clubList']['list'] as $rowcnt=>$v) {
if (is_array($v)) $list=$v; else $list=array();
$list['ROWVAL']=$v;
$list['ROWCNT']=$rowcnt;
$list['ROWBIT']=$rowcnt%2;
$_obj=&$list;
?>
    <li><a id="cid-<?php
echo $_obj['cid'];
?>
" href="./?cid=<?php
echo $_obj['cid'];
?>
"><?php
echo $_obj['cname'];
?>
<i class="web-icon chev-right"></i></a></li>
    <?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
    <li><a id="cid-0" class="last-child" href="./">全部<i class="web-icon chev-right"></i></a></li>
  </ul>
</div>
<script type="text/javascript">$(".nav-links li a:first").addClass("first-child");$("#cid-<?php
echo $_obj['clubList']['current']['cid'];
?>
").addClass("active");</script>
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
      <div class="sponsor-list hidden">
  <div class="sponsor-inner">
    <div class="sponsor-content">
		
    </div>
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
      <div class="content-header">
        <div class="header-inner">
          <h2><?php
if ($_obj['clubList']['current']['cid'] > "0"){
?><?php
echo $_obj['clubList']['current']['cname'];
?>
<?php
} else {
?>全部<?php
}
?></h2>
          <a class="newest" title="最新" href="./new.php"><span class="web-icon icon-newest"></span></a>
        </div>
      </div>
      <ol class="stream-items">
        <?php
if (!empty($_obj['topicList']['list'])){
if (!is_array($_obj['topicList']['list']))
$_obj['topicList']['list']=array(array('list'=>$_obj['topicList']['list']));
$_tmp_arr_keys=array_keys($_obj['topicList']['list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['topicList']['list']=array(0=>$_obj['topicList']['list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['topicList']['list'] as $rowcnt=>$v) {
if (is_array($v)) $list=$v; else $list=array();
$list['ROWVAL']=$v;
$list['ROWCNT']=$rowcnt;
$list['ROWBIT']=$rowcnt%2;
$_obj=&$list;
?>
<li class="stream-item" id="item-<?php
echo $_obj['tid'];
?>
">
  <div class="stream-content" data="<?php
echo $_obj['tid'];
?>
">
    <a href="user.php?id=<?php
echo $_obj['uid'];
?>
" class="item-user"><img class="item-avatar" src="<?php
echo $_obj['avatar'];
?>
" alt="<?php
echo $_obj['nickname'];
?>
"><strong class="item-nickname"><?php
echo $_obj['nickname'];
?>
</strong></a>
    <small class="time"><a href="t.php?id=<?php
echo $_obj['tid'];
?>
"><?php
echo $_obj['posttime'];
?>
</a></small>
    <p class="item-message"><?php
echo $_obj['message'];
?>
</p>
    <?php
if ($_obj['smallimg'] != ""){
?>
    <div class="item-picture">
      <img src="<?php
echo $_obj['smallimg'];
?>
" data-expand="<?php
echo $_obj['bigimage'];
?>
" alt="" title="">
    </div>
    <?php
}
?>
    <div class="stream-item-footer">
      <div class="item-actions">
        <a onclick="favTopic(<?php
echo $_obj['tid'];
?>
);" id="favTopic-<?php
echo $_obj['tid'];
?>
"><span class="web-icon icon-fav"></span>收藏</a>
        <?php
if ($_stack[0]['loginInfo']['group'] > "1"){
?>
        <a onclick="deleteTopic(<?php
echo $_obj['tid'];
?>
,0);" id="deleteTopic-<?php
echo $_obj['tid'];
?>
"><span class="web-icon icon-trash"></span>删除</a>
        <?php
}
?>
      </div>
      <?php
if ($_stack[0]['clubList']['current']['cid'] == "0"){
?>
      <a href="./?cid=<?php
echo $_obj['cid'];
?>
"><?php
echo $_obj['clubname'];
?>
</a>
      <span class="point">•</span>
      <?php
}
?>
      <a href="t.php?id=<?php
echo $_obj['tid'];
?>
" id="comment-<?php
echo $_obj['tid'];
?>
"><?php
if ($_obj['comments'] > "0"){
?><strong><?php
echo $_obj['comments'];
?>
</strong> 回复<?php
} else {
?>暂无回复<?php
}
?></a>
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
          <?php
if ($_obj['topicList']['page']['Total'] > "1"){
?>
            <?php
if ($_obj['topicList']['page']['Prev'] != ""){
?>
            <a href="<?php
echo $_obj['topicList']['page']['Prefix'];
?>
<?php
echo $_obj['topicList']['page']['Prev'];
?>
">上一页</a>
            <?php
if ($_obj['topicList']['page']['Next'] != ""){
?>
            <span class="pagination"><?php
echo $_obj['topicList']['page']['Current'];
?>
 / <?php
echo $_obj['topicList']['page']['Total'];
?>
</span>
            <?php
}
?>
            <?php
}
?>
            <?php
if ($_obj['topicList']['page']['Next'] != ""){
?>
            <a href="<?php
echo $_obj['topicList']['page']['Prefix'];
?>
<?php
echo $_obj['topicList']['page']['Next'];
?>
">下一页</a>
            <?php
}
?>
          <?php
} else {
?>
            <span class="pagination"><?php
if ($_obj['topicList']['count'] > "0"){
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
  
  <div id="writeTopic" class="writeTopic">
    <div class="addtopic">
      <div class="content-header">
        <div class="header-inner">
          <?php
if ($_obj['clubList']['current']['cid'] > "0"){
?>
          <h2><?php
echo $_obj['clubList']['current']['cname'];
?>
</h2>
          <?php
} else {
?>
          <div class="select-box">
            <div class="trigger" data-toggle="select" data-field="#cid"><i></i><span>请选择分类</span></div>
            <div class="options">
              <?php
if (!empty($_obj['clubList']['list'])){
if (!is_array($_obj['clubList']['list']))
$_obj['clubList']['list']=array(array('list'=>$_obj['clubList']['list']));
$_tmp_arr_keys=array_keys($_obj['clubList']['list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['clubList']['list']=array(0=>$_obj['clubList']['list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['clubList']['list'] as $rowcnt=>$v) {
if (is_array($v)) $list=$v; else $list=array();
$list['ROWVAL']=$v;
$list['ROWCNT']=$rowcnt;
$list['ROWBIT']=$rowcnt%2;
$_obj=&$list;
?><a href="#" class="item" data-item="<?php
echo $_obj['cid'];
?>
"><?php
echo $_obj['cname'];
?>
</a><?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
            </div>
          </div>
          <script type="text/javascript" src="static/jquery.select.js"></script>
          <?php
}
?>
          <span class="close-write" id="close-write"></span>
        </div>
      </div>
      <div class="content-post">
        <div class="post-inner">
          <form id="topic-form" autocomplete="off">
            <input type="hidden" name="from" value="<?php
echo $_obj['clubList']['current']['cid'];
?>
">
            <input type="hidden" name="cid" id="cid" value="<?php
echo $_obj['clubList']['current']['cid'];
?>
">
            <textarea class="input-body" name="message" rows="5"></textarea>
            <div class="post-button-left">
              <input type="file" name="picture" id="picture">
            </div>
            <div class="post-button-right">
              <span class="text-counter">200</span>
              <button class="submit-button" type="submit">
                <span class="submit-button-text">发布</span>
              </button>
            </div>
            <div class="clear"></div>
          </form>
          <script type="text/javascript">uploadPostPicture("topic");</script>
        </div>
      </div>
    </div>
  </div>
  
</body>
</html>