function uploadAvatar()
{
	$('#avatar-file').uploadify({
		'swf':'static/uploadify.swf',
      	'uploader':'post.php',
      	'method':'post',
      	'formData':{'do':'settingAvatar','COOKIE':'{"phpsay_uname":"'+parseCookie("phpsay_uname")+'","phpsay_secure":"'+parseCookie("phpsay_secure")+'"}'},
      	'buttonClass':'web-icon',
      	'auto':false,
      	'fileObjName':'avatar',
      	'buttonText':'',
      	'width':40,
      	'height':28,
      	'fileTypeDesc':'图片（*.jpg;*.jpeg;*.gif;*.png）',
      	'fileTypeExts':'*.jpg; *.jpeg; *.gif; *.png',
      	'fileSizeLimit':'1024KB',
      	'multi':false,
      	'queueSizeLimit':1,
      	'removeTimeout':0,
      	'overrideEvents':['onDialogClose','onSelectError'],
      	'onSelectError':function(file){
      		if (file) {
      			if (file.size > 1048576) {
      				alertMessage('图片不能超过1MB');
      				return false;
      			}
				var fileExt = file.type.toLowerCase();
				if( fileExt != ".jpg" || fileExt != ".jpeg" || fileExt != ".gif" || fileExt != ".png")
				{
					alertMessage('不被接受的文件格式');
					return false;
				}
      		}
      	},
      	'onSelect':function(file){
        	$(".setting-button").removeAttr("disabled");
        	$(".setting-button").click(function(){
          		$('#avatar-file').uploadify('upload');
        	});
        	$('#avatar-file').uploadify('disable',true);
      	},
      	'onCancel':function(file){
        	$(".setting-button").attr("disabled","disabled");
        	$(".setting-button").unbind();
        	$('#avatar-file').uploadify('disable',false);
      	},
      	'onUploadError':function(file, errorCode, errorMsg, errorString){
        	$(".setting-button").attr("disabled","disabled");
        	$(".setting-button").unbind();
        	$('#avatar-file').uploadify('disable',false);
      	},
		'onUploadStart':function(file) {
			$(".setting-button").attr("disabled","disabled");
		},
      	'onUploadSuccess':function(file, data, response){
        	$('#avatar-file').uploadify('disable',false);
        	$(".setting-button").unbind();
        	data = eval("("+data+")");
        	if (data.result=="success") {
        		alertMessage("头像修改成功");
        		var avatarURL = $(".current-avatar").attr("src") + "?"+Math.random();
        		$(".current-avatar").attr("src",avatarURL);
        		$("#profile-avatar").attr("src",avatarURL);
        	}
        	else{
        		alertMessage("头像保存失败");
        	}
      	}
    });
	
	$(".uploadify-queue").css({"top":"38px","left":"0"});

	$(".setting-button").attr("disabled","disabled");
}

function uploadPostPicture(type)
{
	var txtobj = $("#"+type+"-form textarea[name=message]");
	
	var btnobj = $("#"+type+"-form .submit-button");

	$('#picture').uploadify({
		'swf':'static/uploadify.swf',
		'uploader':'post.php',
		'method':'post',
		'buttonClass':'web-icon',
		'auto':false,
		'fileObjName':'picture',
		'buttonText':'',
		'width':40,
		'height':28,
		'fileTypeDesc':'图片（*.jpg;*.jpeg;*.gif;*.png）',
		'fileTypeExts':'*.jpg; *.jpeg; *.gif; *.png',
		'fileSizeLimit':'2048KB',
		'multi':false,
		'queueSizeLimit':1,
		'removeTimeout':0,
		'overrideEvents':['onDialogClose','onSelectError'],
		'onSelectError':function(file){
			if (file) {
				if (file.size > 2097152) {
					alertMessage('图片不能超过2MB');
					return false;
				}
				var fileExt = file.type.toLowerCase();
				if( fileExt != ".jpg" || fileExt != ".jpeg" || fileExt != ".gif" || fileExt != ".png")
				{
					alertMessage('不被接受的文件格式');
					return false;
				}
			}
		},
		'onSelect':function(file)
		{
			if($.trim(txtobj.val()).length <= 200)
			{
				btnobj.removeAttr("disabled");
			}

			btnobj.attr("data","media").unbind().click(function()
			{
				if(parseCookie("phpsay_secure") == "")
				{
					alertMessage("请先登录");
				}
				else
				{
					if(type == "topic")
					{
						if( $('input[name=cid]').val() === "" || parseInt($('input[name=cid]').val()) < 1 )
						{
							alertMessage("请选择分类");

							return false;
						}
					}

					$('#picture').uploadify('upload');
				}

				return false;
			});

			$('#picture').uploadify('disable',true);
		},
		'onCancel':function(file)
		{
			if($.trim(txtobj.val()).length == 0)
			{
				btnobj.attr("disabled","disabled");
			}

			btnobj.attr("data","text").unbind();

			if(type == "topic")
			{
				btnobj.click(postNewTopic);
			}
			else if(type == "reply")
			{
				btnobj.click(function(){postReplyTopic(false);return false;});
			}

			$('#picture').uploadify('disable',false);
		},
		'onUploadError':function(file, errorCode, errorMsg, errorString)
		{
			if($.trim(txtobj.val()).length == 0)
			{
				btnobj.attr("disabled","disabled");
			}

			btnobj.attr("data","text").unbind();
			
			if(type == "topic")
			{
				btnobj.click(postNewTopic);
			}
			else if(type == "reply")
			{
				btnobj.click(function(){postReplyTopic(false);return false;});
			}
			
			txtobj.removeAttr("disabled");
			
			$('#picture').uploadify('disable',false);
		},
		'onUploadStart':function(file)
		{
			var message = $.trim(txtobj.val());

			if(message == "")
			{
				message = " ";
			}

			if(type == "topic")
			{
				$('#picture').uploadify('settings','formData',{'do':'addTopic',"from":$('input[name=from]').val(),'cid':$('input[name=cid]').val(),'msg':message,'COOKIE':'{"phpsay_uname":"'+parseCookie("phpsay_uname")+'","phpsay_secure":"'+parseCookie("phpsay_secure")+'"}'});
			}
			else if(type == "reply")
			{
				$('#picture').uploadify('settings','formData',{'do':'replyTopic','tid':$('input[name=tid]').val(),'msg':message,'COOKIE':'{"phpsay_uname":"'+parseCookie("phpsay_uname")+'","phpsay_secure":"'+parseCookie("phpsay_secure")+'"}'});
			}

			btnobj.attr("disabled","disabled");
			
			txtobj.attr("disabled","disabled");
		},
		'onUploadSuccess':function(file, data, response)
		{
			$('#picture').uploadify('disable',false);

			if(type == "topic")
			{
				postTopicResult(data);
			}
			else if(type == "reply")
			{
				replyTopicResult(data,false);
			}
		}
	});

	btnobj.attr("disabled","disabled").attr("data","text");

	if(type == "topic")
	{
		btnobj.click(postNewTopic);

		$(".writeTopic").click(function(ev)
		{
			var target = ev.target || ev.srcElement;

			var tag = target.id;

			if( tag )
			{
				if( tag === "writeTopic" || tag === "close-write" )
				{
					$(this).hide();

					$("body").css({overflow:"auto"});
				}
			}
		});
	}
	else if(type == "reply")
	{
		btnobj.click(function(){postReplyTopic(false);return false;});
	}

	txtobj.keyup(function(){textCounter(type+"-form");});
}