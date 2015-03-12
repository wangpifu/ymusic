$(document).ready(function()
{
	if (resetCode == "")
	{
		$('#sendform').ajaxForm( { beforeSubmit:beforeSendForm, success:sendFormResponse, dataType:'json', type:'POST', url:"./passport.php?do=sendPassword" } );
	}
	else
	{
		$('#resetform').ajaxForm( { beforeSubmit:beforeResetForm, success:resetFormResponse, dataType:'json', type:'POST', url:"./passport.php?do=resetPassword&code="+resetCode } );
	}

	$(".ipt").val("").keyup(checkInput).keydown(function(){$(this).prev().hide();});

	$(".btn").attr("disabled","disabled");
});

function checkInput(event)
{
	var e = event || window.event;

	var fid = $(this).parents("form").attr("id");

	if( fid == "sendform" )
	{
		var es = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/.test($("#"+fid+" input[name=email]").val());

		var vc = ($.trim($("#"+fid+" input[name=verify_code]").val()).length == 5) ? true : false;

		if( es && vc )
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

		if( es )
		{
			if( e.keyCode == 13 )
			{
				$("#"+fid+" input[name=verify_code]").focus();
			}

			if( $("#verify_image").length == 0 )
			{
				$(this).parent().next().append('<img src="verify.php?referer=send_password" id="verify_image" title="点击更换" alt="验证码">');

				$("#verify_image").click(function(){
					$(this).attr("src","verify.php?referer=send_password&rd="+Math.random());
				});
			}
		}
	}
	else if( fid == "resetform" )
	{
		var pw = ($("#"+fid+" input[name=password]").val().length >= 6) ? true : false;

		var pc = ($("#"+fid+" input[name=password]").val() == $("#"+fid+" input[name=password_confirm]").val()) ? true : false;

		if( pw && pc )
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

		if( e.keyCode == 13 && pw )
		{
			if( $(this).attr("name") == "password" )
			{
				$("#"+fid+" input[name=password_confirm]").focus();
			}
		}		
	}

	if( $(this).val().length == 0 )
	{
		$(this).prev().show();
	}	
}

function beforeSendForm(formData, jqForm, options)
{
	$(".ipt").attr("readonly",true);

	$(".btn").attr("disabled","disabled");

	$(".btn").hide();

	$(".connectinput:eq(1)").append('<div class="loading"></div>');

	$(".connecttip").html("");

	$("#verify_image").unbind();

	return true;
}

function sendFormResponse(data, statusText, xhr, $form)
{
	if (statusText == "success")
	{
		if (data.result == "success")
		{
			$(".connecttip").html("<font color='#FF6600'>重置密码的链接已发送至您的邮箱</font>");

			$(".ipt").blur();
		}
		else if (data.result == "error")
		{
			if( data.position == 1 )
			{
				$("input[name=email]").val("");

				$(".placeholder:eq(0)").show();

				$("input[name=email]").focus();
			}
			else if( data.position == 2 )
			{
				$("input[name=verify_code]").focus();
			}

			$(".connecttip").html(data.message);
		}
	}
	else
	{
		$(".connecttip").html("请求出错，请重试");
	}

	$("input[name=verify_code]").val("");

	$(".placeholder:eq(1)").show();

	$(".loading").remove();

	$(".btn").show();

	$(".ipt").attr("readonly",false);

	$("#verify_image").attr("src","verify.php?referer=send_password&rd="+Math.random()).click(function(){
		$(this).attr("src","verify.php?referer=send_password&rd="+Math.random());
	});
}

function beforeResetForm(formData, jqForm, options)
{
	$(".ipt").attr("readonly",true);

	$(".btn").hide();

	$(".connectinput:eq(1)").append('<div class="loading"></div>');

	$(".connecttip").html("");

	return true;
}

function resetFormResponse(data, statusText, xhr, $form)
{
	if (statusText == "success")
	{
		if (data.result == "success")
		{
			location.href = "./passport.php";

			return false;
		}
		else if (data.result == "error")
		{
			$(".connecttip").html(data.message);
		}
	}
	else
	{
		$(".connecttip").html("请求出错，请重试");
	}

	$(".loading").remove();
	
	$(".btn").show();

	$(".ipt").attr("readonly",false);
}
