$(document).ready(function()
{
	if( navigator.appName == "Microsoft Internet Explorer" && parseFloat(navigator.appVersion) <= 4 )
	{
		$('body').css({"background-image":"url('static/cover.png')"});

		$('<div class="notice"><div class="title">您的浏览器不受支持。</div><div class="message">我们建议您使用最新版本的 Chrome、Firefox、Safari 或 IE 浏览器。</div></div>').appendTo(document.body);

		return false;
	}

	$(".loginwindow").fadeIn(500);

	if (connect == "")
	{
		$(".qqlogin").click(function(){
			location.href = './connect.php?do=QQLogin';
		});

		$(".emaillogin").click(showLoginView);

		$('#loginform').ajaxForm( { beforeSubmit:beforeLoginForm, success:loginFormResponse, dataType:'json', type:'POST', url:'passport.php?do=Login' } );

		$('#joinform').ajaxForm( { beforeSubmit:beforeJoinForm, success:joinFormResponse, dataType:'json', type:'POST', url:'passport.php?do=SignUp' } );
		
		$(".ipt").keyup(checkInput).keydown(function(){$(this).prev().hide();});
	}
	else
	{
		checkConnectForm();

		$('input[name=nickname]').keyup(checkConnectForm);
		
		$('#connectform').ajaxForm( { beforeSubmit:beforeConnectForm, success:connectFormResponse, dataType:'json', type:'POST', url:'connect.php?do=Join' } );
	}
});

function showLoginView()
{
	$("#loginSelect").fadeOut();
	
	$("#loginView").fadeIn();

	formReset("loginform");

	$('input[name=account]').focus();

	$("#closesignin").fadeIn(function(){
		$(this).click(function(){
			$(this).unbind();
			$(this).fadeOut();
			$("#loginView").fadeOut(function(){
				$(".forgot").hide();
				$(".signup").show();
			});
			$("#loginSelect").fadeIn();
		});
	});
}

function showSignupView()
{
	$("#closesignin").hide(0,function()
	{
		formReset("loginform");

		$("#loginView").slideUp();
		
		formReset("joinform");

		$("#signupView").slideDown(function()
		{
			$('input[name=email]').focus();

			$("#closesignup").fadeIn(function()
			{
				$(this).click(function()
				{
					$(this).unbind();
					
					$(this).hide(0,function()
					{
						$("#signupView").slideUp();
						
						$("#loginView").slideDown(function()
						{
							formReset("joinform");

							$('input[name=account]').focus();

							$("#closesignin").fadeIn();
						});
					});
				});
			});
		});
	});
}

function formReset(f)
{
	$("#"+f+" .ipt").val("");

	$("#"+f+" .placeholder").show();

	if( $("#"+f+" .btn").attr("disabled") != "disabled" )
	{
		$("#"+f+" .btn").attr("disabled","disabled");
	}
}

function checkInput(event)
{
	var e = event || window.event;

	var fid = $(this).parents("form").attr("id");

	if( fid == "loginform" )
	{
		var as = ($.trim($("#"+fid+" input[name=account]").val()).length >= 2) ? true : false;

		var ps = ($("#"+fid+" input[name=password]").val().length >= 6) ? true : false;

		if( as && ps )
		{
			if( $("#"+fid+" .btn").attr("disabled") == "disabled" )
			{
				$("#"+fid+" .btn").removeAttr("disabled");
			}
		}
		else
		{
			if( $("#"+fid+" .btn").attr("disabled") != "disabled" )
			{
				$("#"+fid+" .btn").attr("disabled","disabled");
			}
		}

		if( e.keyCode == 13 && as )
		{
			if( $(this).attr("name") == "account" )
			{
				$("#"+fid+" input[name=password]").focus();
			}
		}
	}
	else if( fid == "joinform" )
	{
		var es = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/.test($("#"+fid+" input[name=email]").val());

		var ns = ($.trim($("#"+fid+" input[name=nickname]").val()).length >= 2) ? true : false;

		var ps = ($("#"+fid+" input[name=password]").val().length >= 6) ? true : false;

		if( es && ns && ps )
		{
			if( $("#"+fid+" .btn").attr("disabled") == "disabled" )
			{
				$("#"+fid+" .btn").removeAttr("disabled");
			}
		}
		else
		{
			if( $("#"+fid+" .btn").attr("disabled") != "disabled" )
			{
				$("#"+fid+" .btn").attr("disabled","disabled");
			}
		}

		if( e.keyCode == 13 )
		{
			if( $(this).attr("name") == "email" )
			{
				if(es)
				{
					$("#"+fid+" input[name=nickname]").focus();
				}
			}
			else if( $(this).attr("name") == "nickname" )
			{
				if(ns)
				{
					$("#"+fid+" input[name=password]").focus();
				}
			}
		}
	}

	if( $(this).val().length == 0 )
	{
		$(this).prev().show();
	}	
}

function placeholderText(f,i,t,d)
{
	var o = $('#'+f+' input[name='+i+']');

	$('#'+f+' .btn').attr("disabled","disabled");

	o.val("");

	o.prev().html(t);

	o.prev().show();

	o.focus();

	o.keydown(function(){
		$(this).prev().html(d);
	});

	setTimeout(function(){o.prev().html(d);},3000);
}

function beforeLoginForm(formData, jqForm, options)
{
	$("#loginform .ipt").attr("readonly",true);

	$("#loginform .btn").hide();

	$("#login-control").append('<div class="loading"></div>');

	$("#closesignin").hide();

	$("#logintip").hide();

	return true;
}

function loginFormResponse(data, statusText, xhr, $form)
{
	if (statusText == "success")
	{
		if (data.result == "success" || data.result == "login")
		{
			location.href = location.href;

			return false;
		}
		else if (data.result == "error")
		{
			if( data.position == 1 )
			{
				placeholderText("loginform","account",data.message,"请输入邮箱或昵称");

				$(".forgot").hide();

				$(".signup").show();
			}
			else if( data.position == 2 )
			{
				placeholderText("loginform","password",data.message,"请输入密码");

				$(".signup").hide();

				$(".forgot").show();
			}
		}
	}
	else
	{
		alert("请求出错，请重试");
	}

	$("#logintip").show();

	$("#closesignin").show();

	$(".loading").remove();
	
	$("#loginform .btn").show();

	$("#loginform .ipt").attr("readonly",false);
}

function beforeJoinForm(formData, jqForm, options)
{
	$("#joinform .ipt").attr("readonly",true);

	$("#joinform .btn").hide();

	$("#signup-control").append('<div class="loading"></div>');

	$("#closesignup").hide();

	return true;
}

function joinFormResponse(data, statusText, xhr, $form)
{
	if (statusText == "success")
	{
		if( data.result == "login" )
		{
			alert("请先退出登录");

			return false;
		}
		if (data.result == "success" )
		{
			location.href = location.href;

			return false;
		}
		else if (data.result == "error")
		{
			if( data.position == 0 )
			{
				alert(data.message);
			}
			else if( data.position == 1 )
			{
				placeholderText("joinform","email",data.message,"请输入邮件地址");
			}
			else if( data.position == 2 )
			{
				placeholderText("joinform","nickname",data.message,"请输入您的昵称");
			}
			else if( data.position == 3 )
			{
				placeholderText("joinform","password",data.message,"请设置一个密码");
			}
		}
	}
	else
	{
		alert("网络异常");
	}
	
	$("#closesignup").show();

	$(".loading").remove();

	$("#joinform .btn").show();

	$("#joinform .ipt").attr("readonly",false);
}

function checkConnectForm()
{
	if( $.trim($('input[name=nickname]').val()).length < 1 || $.trim($('input[name=nickname]').val()).length > 13 )
	{
		$('input[name=nickname]').focus();

		if( $(".btn").attr("disabled") != "disabled" )
		{
			$('.btn').attr('disabled','disabled');
		}
	}
	else
	{
		if( $(".btn").attr("disabled") == "disabled" )
		{
			$('.btn').removeAttr('disabled');
		}
	}
}

function beforeConnectForm(formData, jqForm, options)
{
	if( $.trim($('input[name=nickname]').val()).length < 2 )
	{
		$(".connecttip").html("昵称太短了");

		return false;
	}

	if( $.trim($('input[name=nickname]').val()).length > 13 )
	{
		$(".connecttip").html("昵称太长了");

		return false;
	}

	$(".connecttip").html("努力提交中...");

	$(".ipt").attr("readonly",true);

	$(".btn").hide();

	$("#connect-control").append('<div class="loading"></div>');

	return true;
}

function connectFormResponse(data, statusText, xhr, $form)
{
	if (statusText == "success")
	{
		if (data.status == "success")
		{
			$(".connecttip").html("保存成功！");

			location.href = location.href;

			return false;
		}
		else if (data.status == "error")
		{
			$(".connecttip").html(data.response);
		}
		else
		{
			$(".connecttip").html("服务器异常，请刷新页面");
		}
	}
	else
	{
		$(".connecttip").html("请求出错，请重试");
	}

	$(".ipt").attr("readonly",false);
	
	$(".loading").remove();
	
	$(".btn").show();
}