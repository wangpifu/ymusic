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