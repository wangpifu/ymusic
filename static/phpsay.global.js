function searchPHPSay()
{
	var word = $.trim($('input[name=q]').val());

	if( word.length < 2 || word.length > 15 )
	{
		alertMessage("输入字符长度范围：2～15");

		$('input[name=q]').focus();
		
		return false;
	}
}

function goSignIn()
{
	location.href = "./passport.php?return=" + encodeURIComponent(location.pathname + location.search);
}

function locationHash()
{
	var h = window.location.hash;

	if ( h.substring(0,6) == "#reply" )
	{
		$("html,body").animate({scrollTop: $("#item-"+h.substr(6)).offset().top-42}, 1000);
	}
}

function parseCookie(varName)
{
	var srcCookie = window.document.cookie;

	if(srcCookie=="")
	{
		return "";
	}

	var nPos=srcCookie.lastIndexOf(varName+"=");
	
	if(nPos>0)
	{
		if(nPos>=2)
		{
			nPos=srcCookie.indexOf("; "+varName+"=",nPos-2);
		}
		else
		{
			nPos=srcCookie.indexOf("; "+varName+"=");
		}
	}

	if(nPos>=0)
	{
		nPos=srcCookie.indexOf('=',nPos)+1;

		var nTailPos=srcCookie.indexOf("; ",nPos);
		
		if(nTailPos>0)
		{
			return srcCookie.substring(nPos,nTailPos);
		}
		else
		{
			return srcCookie.substr(nPos);
		}
	}

	return "";
}

function hideDropdownMenu()
{
	$(window).unbind("click",hideDropdownMenu);

	$(".dropdown-menu").hide();
}

function rightNavBind()
{
	$("#settings").click(function()
	{
		if( $(".dropdown-menu").is(":hidden") )
		{
			$(window).click(hideDropdownMenu);

			$(".dropdown-menu").show();
		}
		else
		{
			hideDropdownMenu();
		}

		return false;
	});

	$(".write-btn").click(function()
	{
		if( location.pathname.replace("index.php","").substr(-1) == "/" )
		{
			showWriteTopic();
		}
		else
		{
			location.href = "./#write";
		}

		return false;
	});
}

function indexPageOnload(uid)
{
    $(".stream-item").click(showTopic);

    if(window.location.hash.substring(0,6) == "#write")
    {
    	showWriteTopic();
    }

    if( uid > 0 )
    {
    	setTimeout(updateMemberLogin,1500);
    }
}

function alertMessage(msg)
{
	$(".alert-messages .message .message-text").html(msg);

	if ( $(".alert-messages").is(":visible") )
	{
		return false;
	}

	$(".alert-messages").fadeIn(300);

	setTimeout('$(".alert-messages").fadeOut(300)',2000);
}

function notificationRequest()
{
	if ( window.webkitNotifications )
	{
		if ( window.webkitNotifications.checkPermission() !== 0 )
		{
			window.webkitNotifications.requestPermission(function(){
				location.href = "notification.php?status=unread";
			});

			return false;
		}
	}

	location.href = "notification.php?status=unread";
}

function showNotifications()
{
	if ( window.location.pathname.split("/")[1] == "notification.php" )
	{
		return false;
	}

	if ( window.webkitNotifications )
	{
		if ( window.webkitNotifications.checkPermission() === 0 )
		{
			$.post("post.php",{"do":"webkitNotify"},function(data)
			{
				if( parseInt(data.count) > 0 )
				{
					if( parseCookie("notifyCount") == data.count )
					{
						return false;
					}

					var message = data.result+" ";

					if ( data.result.split(", ").length > 1 )
					{
						message += "等人";
					}

					message += "提到了您。";

					var n = window.webkitNotifications.createNotification('mobile_static/apple-touch-icon.png','您有 '+data.count+' 条未读提醒',message);

					n.onclick = function() {this.cancel();window.open('./notification.php?status=unread','PHPSayNotification');};
					
					n.replaceId = 'PHPSayNotification';

					n.show();

					setTimeout(function(){n.cancel();}, 5000);
				}
				
				document.cookie = "notifyCount = " + data.count;

			},"json");
		}
	}
}

function imageZoom(img)
{
	$('<div id="imageZoomMask"></div>').appendTo(document.body).css({position:'absolute',top:0,left:0,'z-index':2002,width:$(document).width(),height:$(document).height(),'background':'#000','opacity':'0.8'}).click(function(){$("#imageZoomMask").remove();$("#imageZoomContent").remove();});

	$('<div id="imageZoomContent"></div>').appendTo(document.body).css({position:'absolute',top:'45px',left:'50%','z-index':2003,cursor:"pointer"}).click(function(){$("#imageZoomMask").remove();$("#imageZoomContent").remove();});

	$("#imageZoomContent").append($('<img src="'+img+'" style="max-width:'+($("#imageZoomMask").width()-300)+'px;max-height:'+($("#imageZoomMask").height()-160)+'px;" />').hide().load(function()
	{
		var margin_top = $(window).scrollTop();

		if( $(this).height() < $(window).height() )
		{
			margin_top = ($(window).height()-$(this).height())/2 - 30 + margin_top;
		}

		$(this).parent().css({'margin-top':margin_top+"px",'margin-left':-($(this).width()/2)+"px"});

		$(this).fadeIn();
	}));
}

function updateMemberLogin()
{
	if ( parseCookie("phpsay_logintime") == "" )
	{
		$.get("passport.php?do=loginTime",function(data)
		{
			var addBalance = parseInt(data.balance);

			if ( data.result == "success" && addBalance > 0 )
			{
				alertMessage("今日登录奖励 "+addBalance+" 个社区币");

				$('.balance-count strong').html((parseInt($('.balance-count strong').html())+addBalance));
			}
		},"json");
	}
}

function checkEmail(email)
{
	var patten = new RegExp(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/);

	return patten.test(email);
}

function checkEmailInput()
{
	if( checkEmail($("#user_email").val()) && $("#user_email").val() != $("#email_fieldset label").attr("data") && $("#current_password").val().length >= 6 )
	{
		$(".setting-button").removeAttr("disabled");
	}
	else
	{
		$(".setting-button").attr("disabled","disabled");
	}
}

function settingEmail()
{
	$("#emailSetting input").attr("readonly",true);

	$(".setting-button").attr("disabled","disabled");

	$(".setting-button").html("正在保存");

	$.post("post.php",{"do":"settingEmail","password":$('#current_password').val(),"email":$('#user_email').val()},function(data)
	{
		if (data.result == "success")
		{
			alertMessage("邮箱设置成功");

			$("#email_fieldset label").attr("data",$('#user_email').val());
			
			$("#current_password").val("");
		}
		else
		{
			alertMessage(data.message);
		}

		$("#emailSetting input").attr("readonly",false);

		$(".setting-button").html("保存设置");

	},"json");

	return false;
}

function checkPasswordInput()
{
	var buttonStatus = false;

	if( $("#user_password").val().length >= 6 && $("#user_password").val() == $("#user_password_confirmation").val() )
	{
		buttonStatus = true;
	}

	if( $("#control-current-password").css('display') != "none" )
	{
		if( $("#current_password").val().length < 6 || $("#current_password").val() == $("#user_password").val() )
		{
			buttonStatus = false;
		}
	}

	if( buttonStatus )
	{
		$(".setting-button").removeAttr("disabled");
	}
	else
	{
		$(".setting-button").attr("disabled","disabled");
	}
}

function settingPassword()
{
	$("#passwdSetting input").attr("readonly",true);

	$(".setting-button").attr("disabled","disabled");

	$(".setting-button").html("正在保存");

	$.post("post.php",{"do":"settingPassword","currentPasswd":$('#current_password').val(),"userPasswd":$('#user_password').val()},function(data)
	{
		if (data.result == "success")
		{
			alertMessage("密码设置成功");

			$("#current_password").val("");
			
			$("#user_password").val("");

			$("#user_password_confirmation").val("");

			if( $("#control-current-password").css('display') == "none" )
			{
				$("#control-current-password").slideDown();

				$("#control-user-password label").html("新密码");
			}
		}
		else
		{
			alertMessage(data.message);
		}

		$("#passwdSetting input").attr("readonly",false);

		$(".setting-button").html("保存设置");

	},"json");

	return false;
}

function textCounter(fname)
{
	var textArray = $('#'+fname+' textarea[name=message]').val().replace("\r","").split("\n");

	var arrayLen = textArray.length;

	if( arrayLen > 1)
	{
		var textString = "";

		for(var i=0;i<arrayLen;i++)
		{
			if( $.trim(textArray[i]) != "" )
			{
				textString += textArray[i].replace("　　","");

				if(i < (arrayLen-1))
				{
					textString += "\n";
				}
			}
		}

		$('#'+fname+' textarea[name=message]').val(textString);
	}

	var len = $.trim($('#'+fname+' textarea[name=message]').val()).length;
	
	if( len < 1 )
	{
		if ($("#"+fname+" .submit-button").attr("disabled") != "disabled" && $("#"+fname+" .submit-button").attr("data") == "text")
		{
			$("#"+fname+" .submit-button").attr("disabled","disabled");
		}
	}
	else if( len > 200 )
	{
		if ($("#"+fname+" .submit-button").attr("disabled") != "disabled")
		{
			$("#"+fname+" .submit-button").attr("disabled","disabled");
		}		
	}
	else
	{
		if ($("#"+fname+" .submit-button").attr("disabled") == "disabled")
		{
			$("#"+fname+" .submit-button").removeAttr("disabled");
		}
	}

	len = 200 - len;

	$("#"+fname+" .text-counter").html(len);

	if( len < 11 )
	{
		$("#"+fname+" .text-counter").css("color","#D40D12");

		return false;
	}

	if( len < 21 )
	{
		$("#"+fname+" .text-counter").css("color","#5C0002");

		return false;
	}

	$("#"+fname+" .text-counter").css("color","#999999");
}

function showWriteTopic()
{
	$("body").css({overflow:"hidden"});

	$(".writeTopic").css({"width":$(document).width(),"height":$(document).height()}).fadeIn(100);

	$(".writeTopic .addtopic").css({"top":$(window).scrollTop()+($(window).height()/2)-($(".writeTopic .addtopic").height()/2)});
}

function postNewTopic()
{
	if( $('input[name=cid]').val() === "" || parseInt($('input[name=cid]').val()) < 1 )
	{
		alertMessage("请选择分类");

		return false;
	}

	$(".submit-button").attr("disabled","disabled");

	$('textarea[name=message]').attr("disabled","disabled");

	$.post("post.php",{"do":"addTopic","from":$('input[name=from]').val(),"cid":$('input[name=cid]').val(),"msg":$.trim($('textarea[name=message]').val())},function(result)
	{
		postTopicResult(result);
	});

	return false;
}

function postTopicResult(str)
{
	data = eval("("+str+")");

	if (data.result == "login")
	{
		goSignIn();

		return false;
	}

	$('textarea[name=message]').removeAttr("disabled");

	$(".submit-button").attr("data","text").unbind().click(postNewTopic);

	if (data.result == "success")
	{
		$(".writeTopic").fadeOut(100);

		$("body").css({overflow:"auto"});

		$("html,body").animate({scrollTop:0}, 300);

		$('textarea[name=message]').val('');

		$(".text-counter").html("200");

		$(".stream-items").prepend(data.message);

		$('.stream-items li:eq(0) .stream-content').click(showTopic);

		$('.balance-count strong').html((parseInt($('.balance-count strong').html())+parseInt(data.balance)));

		alertMessage("发布成功");
	}
	else
	{
		if( $.trim($('textarea[name=message]').val()).length >= 1 )
		{
			$(".submit-button").removeAttr("disabled");
		}

		if (data.result == "error")
		{
			alertMessage(data.message);
		}
		else
		{
			alertMessage("服务器异常");
		}
	}
}

function postReplyTopic(isquick)
{
	if( parseCookie('phpsay_secure') == "" )
	{
		alertMessage("请先登录");

		return false;
	}

	$("#reply-form .submit-button").attr("disabled","disabled");

	$('#reply-form textarea[name=message]').attr("disabled","disabled");

	$.post("post.php",{"do":"replyTopic","tid":$('#reply-form input[name=tid]').val(),"msg":$.trim($('#reply-form textarea[name=message]').val())},function(result)
	{
		replyTopicResult(result,isquick);
	});
}

function replyTopicResult(str,isquick)
{
	data = eval("("+str+")");

	$('#reply-form textarea[name=message]').removeAttr("disabled");

	$("#reply-form .submit-button").attr("data","text").unbind().click(function(){postReplyTopic(isquick);return false;});

	if (data.result == "success")
	{
		if (!isquick)
		{
			$('#reply-form textarea[name=message]').val('');

			$("#reply-form .text-counter").html("200");

			$(".reply-items").append(data.message);

			$("html,body").animate({scrollTop: $(".topic-end-inner").offset().top}, 500);
		}
		else
		{
			$(".stream-items .stream-item .quickreply-form").slideUp(function()
			{
				$(this).prev().unbind().click(quickReply);

				$(this).remove();
			});
		}

		$('.balance-count strong').html((parseInt($('.balance-count strong').html())+parseInt(data.balance)));

		alertMessage("回复成功");
	}
	else
	{
		if( $.trim($('#reply-form textarea[name=message]').val()).length >= 1 )
		{
			$("#reply-form .submit-button").removeAttr("disabled");
		}

		if (data.result == "login")
		{
			alertMessage("请先登录");
		}
		else if (data.result == "error")
		{
			alertMessage(data.message);
		}
		else
		{
			alertMessage("服务器异常");
		}
	}
}

function checkIsGoOn(ev)
{
	var target = ev.target || ev.srcElement;

	var tag = target.tagName;

	if ( tag )
	{
		if( tag.toLowerCase() === 'p' || tag.toLowerCase() === 'div' )
		{
			return true;
		}
	}

	return false;
}

function showTopic(ev)
{
	if ( checkIsGoOn(ev) )
	{
		//location.href = "www.baidu.com";
		location.href = "lives.php?id="+$(this).attr("value");
	}
}

function quickReply(ev)
{
	if ( checkIsGoOn(ev) )
	{
		$(".stream-items .stream-item .quickreply-form").slideUp(function()
		{
			$(this).prev().removeAttr("style").unbind().click(quickReply);

			$(this).remove();
		});

		$(this).attr("style","background-color:#FFF");

		var replyArray = $(this).attr("data").split("@");

		var quickReplyForm = '<div class="quickreply-form">';

		quickReplyForm += '<form id="reply-form">';
		
		if( parseInt(replyArray[1]) > 0 )
		{
			quickReplyForm += '<input type="hidden" name="tid" value="'+replyArray[1]+'">';
		}
		else
		{
			quickReplyForm += '<input type="hidden" name="nickname" value="'+replyArray[0]+'">';
		}
		
		quickReplyForm += '<textarea class="input-body" name="message" rows="2"></textarea>';
		
		quickReplyForm += '<div class="post-button-right">';
		
		quickReplyForm += '<span class="text-counter"></span>';
		
		quickReplyForm += '<button class="submit-button" type="submit" data="text" disabled="disabled"><span class="submit-button-text">回复</span></button>';
		
		quickReplyForm += '</div>';
		
		quickReplyForm += '<div class="clear"></div>';
		
		quickReplyForm += '</form>';
		
		quickReplyForm += '</div>';

		$(this).unbind();

		$(this).after(quickReplyForm);

		$(this).next().slideDown(function()
		{
			$("#reply-form textarea[name=message]").focus();

			if( parseInt(replyArray[1]) > 0 )
			{
				$("#reply-form textarea[name=message]").val('@'+replyArray[0]+' ');

				$("#reply-form .submit-button").removeAttr("disabled");
			}

			$("#reply-form textarea[name=message]").keyup(function(){textCounter("reply-form");});
			
			$("#reply-form .submit-button").click(function()
			{
				if( parseInt(replyArray[1]) > 0 )
				{
					postReplyTopic(true);
				}
				else
				{
					postPrivateMessage(true);
				}

				return false;
			});
			
			$(this).prev().click(function(event)
			{
				if ( checkIsGoOn(event) )
				{
					$(this).removeAttr("style");

					$(this).next().slideUp(function()
					{
						$(this).prev().unbind().click(quickReply);

						$(this).remove();
					});
				}
			});
		});
	}
}

function postPrivateMessage(isreply)
{
	$(".submit-button").attr("disabled","disabled");

	$('textarea[name=message]').attr("disabled","disabled");

	$.post("post.php",{"do":"addPrivateMessage","nickname":$('input[name=nickname]').val(),"msg":$.trim($('textarea[name=message]').val())},function(data)
	{
		if (data.result == "success")
		{
			if( isreply )
			{
				$(".stream-items .stream-item .quickreply-form").slideUp(function()
				{
					$(this).prev().unbind().click(quickReply);

					$(this).remove();
				});

				$('.balance-count strong').html((parseInt($('.balance-count strong').html())+parseInt(data.balance)));

				alertMessage("私信回复成功");
			}
			else
			{
				alertMessage("私信发送成功");

				setTimeout(function(){location.href=location.href},2000);
			}
		}
		else
		{
			$('textarea[name=message]').removeAttr("disabled");

			$(".submit-button").removeAttr("disabled");

			if (data.result == "login")
			{
				alertMessage("请先登录");
			}
			else if (data.result == "error")
			{
				alertMessage(data.message);
			}
			else
			{
				alertMessage("服务器异常");
			}
		}
	},"json");
}

function deleteTopic(tid,cid)
{
	var o = $("#deleteTopic-"+tid);

	o.removeAttr("onclick");

	if( cid == 0 )
	{
		o.html(o.html().replace("删除","确认"));
	}
	else
	{
		o.html("确认删除");
	}

	o.unbind();

	o.click(function()
	{
		if( cid == 0 )
		{
			o.hide();
		}
		else
		{
			o.parent().parent().hide();
		}

		$.post("post.php",{"do":"deleteTopic","tid":tid},function(data)
		{
			if ( data.result == "success" )
			{
				if( cid == 0 )
				{
					$("#item-"+tid).slideUp(300,function(){$(this).remove();alertMessage("删除成功");});
				}
				else
				{
					location.href = "./?cid="+cid;
				}
			}
			else
			{
				alertMessage("删除失败");

				if( cid == 0 )
				{
					o.show();
				}
				else
				{
					o.parent().parent().show();
				}
			}
		},"json");
	});

	setTimeout(function(){
		if( cid == 0 ){
			o.html(o.html().replace("确认","删除"));
		}
		else{
			o.html("彻底删除");
		}
		o.unbind().click(function(){deleteTopic(tid,cid);});
	},3000);
}

function deleteTopicPicture(tid)
{
	var o = $("#deleteTopicPicture-"+tid);

	o.removeAttr("onclick");

	o.html("确认删除");

	o.unbind();

	o.click(function()
	{
		$(".trash-topic").hide();

		$(".trash-type").hide();

		o.hide();

		$.post("post.php",{"do":"deleteTopicPicture","tid":tid},function(data)
		{
			$(".trash-topic").show();

			if ( data.result == "success" )
			{
				$(".topic-picture").remove();

				o.remove();
			}
			else
			{
				alertMessage("图片删除失败");

				o.show();
			}
		},"json");
	});

	setTimeout(function(){o.html("删除图片").unbind().click(function(){deleteTopicPicture(tid)})},3000);
}

function replyAt(uname)
{
	$('#reply-form textarea[name=message]').val($('#reply-form textarea[name=message]').val().replace("@"+uname+" ","")+"@"+uname+" ");

	$('#reply-form textarea[name=message]').focus();

	$('#reply-form textarea[name=message]').focusEnd();

	$("html,body").animate({scrollTop: $(".reply-form").offset().top-42}, 300);

	textCounter("reply-form");
}

function deleteReply(pid)
{
	var o = $("#deleteReply-"+pid);

	o.removeAttr("onclick");

	o.html(o.html().replace("删除","确认"));

	o.unbind();

	o.click(function(){
		$.post("post.php",{"do":"deleteReply","pid":pid},function(data){
			if (data.result == "success") {
				$("#item-"+pid).slideUp(300,function(){$(this).remove();alertMessage("删除成功");});
			}
			else {
				alertMessage("删除失败");
				o.show();
			}
		},"json");

		o.hide();
	});

	setTimeout(function(){o.html(o.html().replace("确认","删除")).unbind().click(function(){deleteReply(pid)})},3000);
}

function deleteNotification(nid)
{
	var o = $("#deleteNotification-"+nid);

	o.removeAttr("onclick");

	o.html(o.html().replace("删除","确认"));

	o.unbind();

	o.click(function(){
		$.post("post.php",{"do":"deleteNotification","nid":nid},function(data){
			if (data.result == "success") {
				$("#item-"+nid).slideUp(300,function(){$(this).remove();alertMessage("删除成功");});
			}
			else {
				alertMessage("删除失败");
				o.show();
			}
		},"json");

		o.hide();
	});

	setTimeout(function(){o.html(o.html().replace("确认","删除")).unbind().click(function(){deleteNotification(nid)})},3000);
}

function updateFavNum(num)
{
	var o = $(".favorite-count").children("strong");

	if (o.length > 0)
	{
		o.html(parseInt(o.html()) + num);
	}
}

function favTopic(tid)
{
	var o = $("#favTopic-"+tid);

	o.hide();

	$.post("post.php",{"do":"favTopic","tid":tid},function(data)
	{
		if (data.result == "success")
		{
			o.addClass("favorite");

			o.html(o.html().replace("收藏","已收藏"));

			o.attr("onclick","unFavTopic("+tid+")");

			if (data.message >= 1)
			{
				updateFavNum(1);
			}
		}
		else if (data.result == "login")
		{
			alertMessage("请先登录");
		}
		else
		{
			alertMessage("收藏失败");
		}
		o.show();
	},"json");
}

function unFavTopic(tid)
{
	var o = $("#favTopic-"+tid);

	o.hide();

	$.post("post.php",{"do":"unFavTopic","tid":tid},function(data)
	{
		if (data.result == "success")
		{
			o.removeClass("favorite");

			o.html(o.html().replace("已收藏","收藏"));

			o.attr("onclick","favTopic("+tid+")");

			updateFavNum(-1);
		}
		else
		{
			alertMessage("取消收藏失败");
		}
		o.show();
	},"json");
}

function likeReply(pid)
{
	var o = $("#likeReply-"+pid);

	o.removeAttr("onclick");

	$.post("post.php",{"do":"likeReply","pid":pid},function(data)
	{
		if (data.result == "success")
		{
			if( data.message == "1" )
			{
				var likes = parseInt(o.html().replace(/<\/?[^>]*>/g,'').replace(/[^0-9]/ig,""));

				o.html(o.html().replace("("+likes+")","("+(likes+1)+")"));
			}

			o.addClass("like").attr("onclick","unLikeReply("+pid+")");

			return false;
		}
		else if (data.result == "error")
		{
			if( data.message == "-1" )
			{
				$("#item-"+pid).slideUp(300,function(){$(this).remove();alertMessage("不能赞已删除的回复");});

				return false;
			}
			else if( data.message == "-2" )
			{
				alertMessage("不能给自己赞哦");
			}
		}
		else if (data.result == "login")
		{
			alertMessage("请先登录");
		}

		o.attr("onclick","likeReply("+pid+")");
		
	},"json");
}

function unLikeReply(pid)
{
	var o = $("#likeReply-"+pid);

	o.removeAttr("onclick");

	$.post("post.php",{"do":"unLikeReply","pid":pid},function(data)
	{
		if (data.result == "success")
		{
			var likes = parseInt(o.html().replace(/<\/?[^>]*>/g,'').replace(/[^0-9]/ig,""));

			o.html(o.html().replace("("+likes+")","("+(likes-1)+")"));

			o.removeClass("like").attr("onclick","likeReply("+pid+")");

			return false;
		}
		else if (data.result == "login")
		{
			alertMessage("请先登录");
		}
		else
		{
			alertMessage("操作失败");
		}

		o.attr("onclick","unLikeReply("+pid+")");

	},"json");
}

function sinkTopic(tid)
{
	var o = $("#sinkTopic-"+tid);

	o.hide();

	$.post("post.php",{"do":"topicStatus","tid":tid},function(data)
	{
		if (data.result == "success")
		{
			if(data.message == 1)
			{
				o.html(o.html().replace("恢复","下沉"));

				$(".reply-form").slideDown();
			}
			else
			{
				o.html(o.html().replace("下沉","恢复"));

				$(".reply-form").slideUp();
			}
		}
		else
		{
			alertMessage("操作异常");
		}

		o.show();

	},"json");	
}

function moveTopic()
{
	var o = $(this);

	var p = o.parent();

	var t = p.parent();

	t.hide();

	var cid = $(this).attr("data");

	$.post("post.php",{"do":"moveTopic","tid":$(this).parent().attr("data"),"cid":cid},function(data)
	{
		if (data.result == "success")
		{
			p.hide();

			p.children("li").show();

			o.hide();

			$(".nav-links li a").removeClass("active");

			$(".nav-links li a").each(function()
			{
				if ($(this).attr("href").split("?cid=")[1] == cid)
				{
					$(this).addClass("active");
				};
			});
		}
		else
		{
			alertMessage("操作异常");
		}

		t.show();

	},"json");	
}

function topicAction()
{
	$(".trash-topic").hover(
		function(){
			$(".trash-type").slideDown(150);
		},function(){
			$(".trash-type").slideUp(150);
		}
	);

	if( $(".move-club").children().length > 1 )
	{
		$(".move-topic").hover(
			function(){
				$(".move-club").slideDown(150);
			},function(){
				$(".move-club").slideUp(150);
			}
		);

		$(".move-club li").click(moveTopic);
	}
	else
	{
		$(".move-topic").remove();
	}
}

function blockUser(uid)
{
	$(".block").hide();

	$.post("post.php",{"do":"userGroup","uid":uid},function(data)
	{
		if (data.result == "success")
		{
			if(data.message == 1)
			{
				$(".block").removeClass("blocked");
			}
			else if(data.message == 0)
			{
				$(".block").addClass("blocked");
			}
		}
		else
		{
			alertMessage("操作异常");
		}

		$(".block").show();

	},"json");	
}

function rechargeOnline()
{
	alertMessage("暂未开放");
}

function checkSponsorInput()
{
	var linkText = $.trim($("#linkText").val());

	var linkURL = $.trim($("#linkURL").val());

	var linkId = $('input[name=linkId]').val();

	if( linkText.length > 1 && linkURL.length > 10 && linkURL.replace("s","").substring(0,7) == "http://" )
	{
		if( parseInt(linkId) > 0 )
		{
			if( linkText == $("#sponsor-"+linkId).html() && linkURL.replace(location.protocol+"//"+location.host,"") == $("#sponsor-"+linkId).attr("href")  )
			{
				$(".setting-button").attr("disabled","disabled");
			}
			else
			{
				$(".setting-button").removeAttr("disabled");
			}
		}
		else
		{
			$(".setting-button").removeAttr("disabled");
		}
	}
	else
	{
		$(".setting-button").attr("disabled","disabled");
	}
}

function buySponsor()
{
	$(".setting-button").attr("disabled","disabled");

	$.post("post.php",{"do":"buySponsor","linkText":$.trim($("#linkText").val()),"linkURL":$.trim($("#linkURL").val()),"linkId":$('input[name=linkId]').val()},function(data)
	{
		if (data.result == "success")
		{
			location.href = location.href;
		}
		else
		{
			alertMessage(data.message);

			$(".setting-button").removeAttr("disabled");
		}
	},"json");

	return false;
}

function renewSponsor(sid)
{
	var o = $("#sponsor-status-"+sid);

	o.next().next().hide();

	o.next().hide();

	o.html("正在购买...");

	$.post("post.php",{"do":"renewSponsor","sponsorId":sid},function(data)
	{
		if (data.result == "success")
		{
			$('.balance-count strong').html((parseInt($('.balance-count strong').html())+parseInt(data.balance)));

			o.html("等待审核");
		}
		else
		{
			alertMessage(data.message);

			o.html("已过期");

			o.next().show();

			o.next().next().show();
		}
	},"json");
}

function buyGoods()
{
	var o = $(this);

	o.unbind().addClass("pay-load").html("正在校验");

	$.post("post.php",{"do":"buyGoods","goodsId":o.attr("data")},function(data)
	{
		if (data.result == "login")
		{
			goSignIn();
		}
		else if (data.result == "success")
		{
			if( data.message == "paid" )
			{
				alertMessage("您已购买过此商品，可直接下载");

				o.removeClass("pay-load").addClass("pay-finished").html("下载代码").click(function(){$("#goodsId").val(o.attr("data"));$("#storeFrom").submit();});
			}
			else
			{
				o.click(payGoods).removeClass("pay-load").addClass("pay-confirm").html("确认支付");
			}
		}
		else
		{
			alertMessage(data.message);

			o.click(buyGoods).removeClass("pay-load").html("立即购买");
		}
	},"json");
}

function payGoods()
{
	var o = $(this);

	o.unbind().removeClass("pay-confirm").addClass("pay-load").html("正在支付");

	$.post("post.php",{"do":"buyGoods","goodsId":o.attr("data"),"goodsPay":"2"},function(data)
	{
		if (data.result == "login")
		{
			goSignIn();
		}
		else if (data.result == "success")
		{
			$('.balance-count strong').html((parseInt($('.balance-count strong').html())+parseInt(data.balance)));

			o.removeClass("pay-load").addClass("pay-finished").html("下载代码").click(function(){$("#goodsId").val(o.attr("data"));$("#storeFrom").submit();});
		}
		else
		{
			alertMessage(data.message);

			o.click(buyGoods).removeClass("pay-load").html("立即购买");
		}
	},"json");
}

