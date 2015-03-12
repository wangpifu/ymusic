function loginPHPSay()
{
	var account = $.trim($('input[name=account]').val());

	if( account.length < 2 || account.length > 36 )
	{
		$('input[name=account]').focus();

		return false;
	}

	var password = $('input[name=password]').val();

	if( password.length < 6 || password.length > 26 )
	{
		$('input[name=password]').focus();

		return false;
	}

	$('input[name=account]').attr("readonly",true);

	$('input[name=password]').attr("readonly",true);

	$('.submit-button').attr("value","正在登录...");

	$('.submit-button').attr("disabled","disabled");

	$.post($("#loginForm").attr("action"),{"account":account,"password":password},function(data)
	{
		if (data.result == "success")
		{
			$('.submit-button').attr("value","登录成功，正在跳转...");

			setTimeout("location.href=location.href;",1000);
		}
		else if (data.result == "error")
		{
			$('input[name=account]').attr("readonly",false);

			$('input[name=password]').attr("readonly",false);

			$('.submit-button').removeAttr('disabled');

			$('.submit-button').attr('value','重新登录');

			if( data.position == 1 )
			{
				$('input[name=account]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder","邮箱或昵称")});
			}
			else if( data.position == 2 )
			{
				$('input[name=password]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder","密码")});
			}
		}

	},"json");

	return false;
}

function joinPHPSay()
{
	var nickname = $.trim($('input[name=nickname]').val());

	if( nickname.length < 1 || nickname.length > 13 )
	{
		$('input[name=nickname]').focus();

		return false;
	}

	$('input[name=nickname]').attr("readonly",true);

	$('.submit-button').attr("value","正在提交...");

	$('.submit-button').attr("disabled","disabled");

	$.post($("#joinForm").attr("action"),{"nickname":nickname},function(data)
	{
		if (data.status == "success")
		{
			$('.submit-button').attr("value","保存成功，正在跳转...");

			setTimeout("location.href=location.href;",1000);
		}
		else if (data.status == "error")
		{
			$('.submit-button').removeAttr('disabled');

			$('.submit-button').attr('value','重新提交');

			$('input[name=nickname]').attr("readonly",false).attr("placeholder",data.response).val("").keydown(function(){$(this).attr("placeholder","昵称")});
		}

	},"json");

	return false;
}

function changeVerify()
{
	$(".verify-image").attr("src","verify.php?referer=send_password&rd="+Math.random());
}

function sendPassword()
{
	var email = $('input[name=email]').val();

	if( !/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/.test(email) )
	{
		$('input[name=email]').focus();

		return false;
	}

	var verify_code = $('input[name=verify_code]').val();

	if( verify_code.length != 5 )
	{
		$('input[name=verify_code]').focus();

		return false;
	}	

	$('input[name=email]').attr("readonly",true);

	$('input[name=verify_code]').attr("readonly",true);

	$(".verify-image").unbind();

	$('.submit-button').attr("value","正在提交...");

	$('.submit-button').attr("disabled","disabled");

	$.post($(this).attr("action"),{"email":email,"verify_code":verify_code},function(data)
	{
		if (data.result == "success")
		{
			$('.submit-button').attr("value","发送成功");

			setTimeout("alert('发送成功！请查看您的邮箱');location.href='./passport.php';",500);
		}
		else if (data.result == "error")
		{
			$('input[name=email]').attr("readonly",false);

			$('input[name=verify_code]').attr("readonly",false);

			changeVerify();
			
			$(".verify-image").click(changeVerify);

			$('.submit-button').removeAttr('disabled');

			$('.submit-button').attr('value','重新提交');

			if( data.position == 1 )
			{
				$('input[name=email]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder","邮箱")});

				$('input[name=verify_code]').val("");
			}
			else if( data.position == 2 )
			{
				$('input[name=verify_code]').attr("placeholder","验证码错误").val("").keydown(function(){$(this).attr("placeholder","验证码")});
			}
		}

	},"json");

	return false;
}

function resetPassword()
{
	var password = $('input[name=password]').val();

	if( password.length < 6 )
	{
		$('input[name=password]').focus();

		return false;
	}

	if( $('input[name=password_confirm]').val() != password )
	{
		$('input[name=password_confirm]').focus();

		return false;
	}	

	$('input[name=password]').attr("readonly",true);

	$('input[name=password_confirm]').attr("readonly",true);

	$('.submit-button').attr("value","正在保存...");

	$('.submit-button').attr("disabled","disabled");

	$.post($(this).attr("action"),{"password":password},function(data)
	{
		if (data.result == "success")
		{
			$('.submit-button').attr("value","密码重设成功");

			setTimeout("location.href='./passport.php';",1000);
		}
		else if (data.result == "error")
		{
			$('input[name=password]').attr("readonly",false).val("");

			$('input[name=password_confirm]').attr("readonly",false).val("");

			$('.submit-button').removeAttr('disabled');

			$('.submit-button').attr('value','重新提交');

			alert(data.message);
		}

	},"json");

	return false;
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
				$('#balance-count').html((parseInt($('#balance-count').html())+addBalance));
			}
		},"json");
	}
}

function topicReadyLoad(uid)
{
	$('.item-image img').click(imageZoom);

	if( uid > 0 )
	{
		$("#picture").val("");
		
		$("#reply-topic-form").submit(replyTopic);

		$("#reply-topic-form textarea[name=message]").focusin(function(){$(this).attr("rows","6");}).focusout(function(){$(this).attr("rows","3");});

		$(".like").click(likeReply);
	}

	var h = window.location.hash;

	if ( h.substring(0,6) == "#reply" )
	{
		$("html,body").animate({scrollTop: $("#reply-"+h.substr(6)).offset().top}, 1000);
	}
}

function postTopic()
{
	var cid = $('#cid').val();

	if( parseInt(cid) < 1 )
	{
		alert("请选择分类");

		$('#cid').focus();

		return false;
	}

	var msg = $.trim($('textarea[name=message]').val());

	if( msg.length > 200 )
	{
		$('textarea[name=message]').focus();

		return false;
	}

	if( msg.length < 1 )
	{
		$('textarea[name=message]').focus();

		return false;
	}

	$(".loading").show();

	$("#add-topic-form").hide();

	$.post("post.php",{"do":"addTopic","cid":cid,"msg":msg},function(data)
	{
		if (data.result == "login")
		{
			location.href = "./passport.php";
		}
		else if (data.result == "success")
		{
			location.href = "./?cid=" + $('input[name=cid]').val();
		}
		else if (data.result == "error")
		{
			alert(data.message);

			$(".loading").hide();

			$("#add-topic-form").show();

			$('html, body').animate({scrollTop: $(document).height()}, 300);
		}

	},"json");

	return false;
}

function replyTopic()
{
	var msg = $.trim($('textarea[name=message]').val());
	
	if( msg.length > 200 )
	{
		$('textarea[name=message]').focus();

		return false;
	}

	if( msg.length < 1 )
	{
		$('textarea[name=message]').focus();

		return false;
	}

	$(".loading").show();

	$("#reply-topic-form").hide();
	
	$.post("post.php",{"do":"replyTopic","tid":$('input[name=tid]').val(),"msg":msg},function(data)
	{
		if (data.result == "login")
		{
			location.href = "./passport.php";
		}
		else if (data.result == "success")
		{
			var myDate = new Date();

			location.href = "./t.php?id=" + $('input[name=tid]').val() + "&rd=" + myDate.getTime() + "#reply" + data.message;
		}
		else if (data.result == "error")
		{
			alert(data.message);

			$(".loading").hide();

			$("#reply-topic-form").show();
		}

	},"json");

	return false;
}

function replyAt(uname)
{
	$('textarea[name=message]').val($('textarea[name=message]').val().replace("@"+uname+" ","")+"@"+uname+" ");

	$('textarea[name=message]').focus();
}

function postPrivateMessage()
{
	var msg = $.trim($('textarea[name=message]').val());
	
	if( msg.length < 1 || msg.length > 200 )
	{
		$('textarea[name=message]').focus();

		return false;
	}

	$('textarea[name=message]').attr("disabled","disabled");

	$(".submit-button").attr("value","正在发送...").attr("disabled","disabled");

	$.post("post.php",{"do":"addPrivateMessage","nickname":$('input[name=nickname]').val(),"msg":msg},function(data)
	{
		if (data.result == "login")
		{
			location.href = "./passport.php";
		}
		else if (data.result == "success")
		{
			alert("私信已发送");

			location.href = location.href;
		}
		else if (data.result == "error")
		{
			alert(data.message);

			$('textarea[name=message]').removeAttr("disabled");

			$(".submit-button").removeAttr("disabled").attr("value","重新发送");
		}

	},"json");

	return false;
}

function deleteNotify(nid)
{
	if(confirm("确定要删除该条提醒吗？"))
	{
		$("#notify-"+nid).slideUp();

		$.post("post.php",{"do":"deleteNotification","nid":nid},function(data)
		{
			if (data.result == "success")
			{
				$("#notify-"+nid).remove();
			}
			else
			{
				$("#notify-"+nid).slideDown();
			}
		},"json");
	}
}

function uploadAvatar()
{
	if( $("input[type='file']").val() == "" )
	{
		alert("请先选择一张图片");
		
		return false;
	}
	
	if( !/.(gif|jpg|jpeg|png)$/.test( $("input[type='file']").val().toLowerCase() ) )
	{
		alert("您只能上传 JPG/GIF/PNG");
		
		return false;
	}
	
	$(".current-avatar").append('<div style="position:absolute;z-index:1000;width:180px;height:50px;top:0;left:65px;background:url(./mobile_static/loading.gif) no-repeat;background-size:30px 30px;background-position:0 20px;"></div>');
	
	$(".submit-button").attr("value","正在上传");
	
	$(".submit-button").attr("disabled","disabled");
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

function checkSettingResult()
{
	var result = parseCookie("upload_avatar_result");

	if( result != "" )
	{
		document.cookie = "upload_avatar_result=";

		if( result == "SUCCESS" )
		{
			var avatarURL = $(".current-avatar img").attr("src") + "?" + Math.random();

			$(".user img").attr("src",avatarURL);
			
			$(".current-avatar img").attr("src",avatarURL);
		}
		else
		{
			alert(decodeURIComponent(result));
		}
	}
}

function settingPassword()
{
	var password_current = $('input[name=password_current]').val();

	if( $(".user-input:eq(0)").css('display') != "none" )
	{
		if( password_current.length < 6 )
		{
			$('input[name=password_current]').focus();

			return false;
		}
	}

	var password = $('input[name=password]').val();

	var placeholderText = $('input[name=password]').attr("placeholder");

	if( password.length < 6 || password.length > 26 )
	{
		$('input[name=password]').attr("placeholder","密码至少6位，最多26位").val("").focus().keydown(function(){$(this).attr("placeholder",placeholderText)});

		return false;
	}

	if( password == password_current )
	{
		$('input[name=password]').attr("placeholder","新密码不能与当前密码相同").val("").focus().keydown(function(){$(this).attr("placeholder",placeholderText)});

		$('input[name=password_confirm]').val("");

		return false;
	}

	if( password != $('input[name=password_confirm]').val() )
	{
		$('input[name=password_confirm]').attr("placeholder","两次输入的密码不一致").val("").focus().keydown(function(){$(this).attr("placeholder","确认密码")});

		return false;
	}

	$('.user-input input').attr("readonly",true);

	$('.submit-button').attr("value","正在保存...");

	$('.submit-button').attr("disabled","disabled");

	$.post($(this).attr("action"),{"do":"settingPassword","currentPasswd":password_current,"userPasswd":password},function(data)
	{
		if (data.result == "success")
		{
			$('.user-input input').val("");

			if( $(".user-input:eq(0)").css('display') == "none" )
			{
				$(".user-input:eq(0)").slideDown();

				$('input[name=password]').attr("placeholder","新密码");

				$(".submit-button").attr("value","密码设置成功！");
			}
			else
			{
				$(".submit-button").attr("value","密码修改成功！");
			}

			setTimeout('$(".submit-button").attr("value","保 存");',3500);
		}
		else if (data.result == "error")
		{
			$('.submit-button').attr('value','重新提交');

			if( data.position == 1 )
			{
				$('input[name=password_current]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder","当前密码")});
			}
			else if( data.position == 2 )
			{
				$('input[name=password]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder",placeholderText)});

				$('input[name=password_confirm]').val("");
			}
		}

		$('.user-input input').attr("readonly",false);

		$('.submit-button').removeAttr('disabled');

	},"json");

	return false;
}

function settingEmail()
{
	var password = $('input[name=password]').val();

	if( password.length < 6 )
	{
		$('input[name=password]').focus();

		return false;
	}

	var email = $('input[name=email]').val();

	if( !/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/.test(email) )
	{
		$('input[name=email]').focus();

		return false;
	}

	$('.user-input input').attr("readonly",true);

	$('.submit-button').attr("value","正在保存...");

	$('.submit-button').attr("disabled","disabled");

	$.post($(this).attr("action"),{"do":"settingEmail","password":password,"email":email},function(data)
	{
		if (data.result == "success")
		{
			$('input[name=password]').val("");

			$('.submit-button').attr('value','保存成功！');

			setTimeout("$('.submit-button').attr('value','保 存');",3500);
		}
		else if (data.result == "error")
		{
			$('.submit-button').attr('value','重新提交');

			if( data.position == 1 )
			{
				$('input[name=password]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder","当前密码")});
			}
			else if( data.position == 2 )
			{
				$('input[name=email]').attr("placeholder",data.message).val("").keydown(function(){$(this).attr("placeholder","邮件地址")});
			}
		}

		$('.user-input input').attr("readonly",false);

		$('.submit-button').removeAttr('disabled');

	},"json");
	
	return false;
}

function favoriteTopic(tid)
{
	$("#topicFav").removeAttr("onclick").fadeTo(100,0.1);

	$.post("post.php",{"do":"favTopic","tid":tid},function(data)
	{
		if (data.result == "success")
		{
			$("#topicFav").fadeTo(100,0.5,function(){$(this).addClass("favorited").removeClass("favorite").attr("onclick","unFavoriteTopic("+tid+")")});
		}
		else
		{
			$("#topicFav").fadeTo(100,0.5).attr("onclick","favoriteTopic("+tid+")");
		}
	},"json");
}

function unFavoriteTopic(tid)
{
	$("#topicFav").removeAttr("onclick").fadeTo(100,0.1);

	$.post("post.php",{"do":"unFavTopic","tid":tid},function(data)
	{
		if (data.result == "success")
		{
			$("#topicFav").fadeTo(100,0.5,function(){$(this).addClass("favorite").removeClass("favorited").attr("onclick","favoriteTopic("+tid+")")});
		}
		else
		{
			$("#topicFav").fadeTo(100,0.5).attr("onclick","unFavoriteTopic("+tid+")");
		}
	},"json");
}

function deleteTopic(tid,cid)
{
	if(confirm("确定要删除该条消息及其回复吗？"))
	{
		$(".delete").hide();

		$.post("post.php",{"do":"deleteTopic","tid":tid},function(data)
		{
			if (data.result == "success")
			{
				location.href = "./?cid="+cid;
			}
			else
			{
				$(".delete").show();
			}
		},"json");
	}
}

function deleteReply(pid)
{
	if(confirm("确定要删除该条回复吗？"))
	{
		$("#reply-"+pid).slideUp();

		$.post("post.php",{"do":"deleteReply","pid":pid},function(data)
		{
			if (data.result == "success")
			{
				$("#reply-"+pid).remove();
			}
			else
			{
				$("#reply-"+pid).slideDown();
			}
		},"json");
	}
}

function likeReply()
{	
	var o = $(this);

	o.unbind().fadeTo(100,0.1);

	$.post("post.php",{"do":"likeReply","pid":$(this).attr("data")},function(data)
	{
		if (data.result == "success")
		{
			o.fadeTo(100,0.5,function(){$(this).addClass("liked").removeClass("like").click(unLikeReply)});
		}
		else
		{
			o.fadeTo(100,0.6,function(){$(this).click(likeReply)});
		}
	},"json");
}

function unLikeReply()
{
	var o = $(this);

	o.unbind().fadeTo(100,0.1);

	$.post("post.php",{"do":"unLikeReply","pid":$(this).attr("data")},function(data)
	{
		if (data.result == "success")
		{
			o.fadeTo(100,0.6,function(){$(this).addClass("like").removeClass("liked").click(likeReply)});
		}
		else
		{
			o.fadeTo(100,0.5,function(){$(this).click(unLikeReply)});
		}
	},"json");
}

function imageZoom()
{
	$('<div class="zoom-mask"></div>').appendTo(document.body).css({width:$(document).width()+"px",height:$(document).height()+"px",'background-position':'center '+(($(window).height()-16)/2+$(window).scrollTop())+'px'}).click(function(){$(".zoom-mask").remove();$(".zoom-content").remove();});

	$('<div class="zoom-content"></div>').appendTo(document.body).click(function(){$(".zoom-mask").remove();$(".zoom-content").remove();});

	$(".zoom-content").append($('<img src="'+$(this).attr("rel")+'" style="max-width:'+($(".zoom-mask").width()-10)+'px;max-height:'+($(".zoom-mask").height()-10)+'px;" />').hide().load(function()
	{
		$(".zoom-mask").css({'background-image':'none'});

		var margin_top = $(window).scrollTop();

		if( $(this).height() < $(window).height() )
		{
			margin_top = ($(window).height()-$(this).height())/2 + margin_top;
		}

		$(this).parent().css({'margin-top':margin_top,'margin-left':-($(this).width()/2)+"px"});

		$(this).fadeIn();
	}));
}