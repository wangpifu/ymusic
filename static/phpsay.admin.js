function deleteSponsor(sid)
{
	if( !confirm("确定要删除吗？") )
	{
		return false;
	}

	$.post("admin.php",{"do":"sponsor_delete","Id":sid},function(data)
	{
		if( data.result == "success" )
		{
			location.href = location.href;
		}
		else
		{
			alertMessage("删除失败");
		}

	},"json");
}

function lineSponsor(sid,status)
{
	var statusText = (status == 1) ? "上" : "下";

	if( !confirm("确定要"+statusText+"线吗？") )
	{
		return false;
	}

	$.post("admin.php",{"do":"sponsor_status","Id":sid,"Status":status},function(data)
	{
		if( data.result == "success" )
		{
			location.href = location.href;
		}
		else
		{
			alertMessage("操作失败");
		}

	},"json");
}

function saveSponsorOrder()
{
	var sponsorIds = $(".sponsor-items li").map(function() { return $(this).attr("data"); }).get().join(",");

	$.post("admin.php",{"do":"sponsor_order","Ids":sponsorIds},function(data)
	{
		if( parseInt(data.message) < 2 )
		{
			alertMessage("排序系统异常");
		}

	},"json");
}

function editSponsor(sid)
{
	$("#linkText").val($("#sponsor-"+sid).html());

	var linkURL = $("#sponsor-"+sid).attr("href");

	if( linkURL.substr(0,1) == "/" )
	{
		linkURL = location.protocol + "//" + location.host + linkURL;
	}

	$("#linkURL").val(linkURL);

	$('input[name=linkId]').val(sid);

	$('button.setting-button').html("保存修改");
}

function checkClubInput()
{
	if( $.trim($(this).val()).length > 1 && $.trim($(this).val()) != $(this).parent().attr("data") )
	{
		$(this).parents("form").find("button.setting-button").removeAttr("disabled");
	}
	else
	{
		$(this).parents("form").find("button.setting-button").attr("disabled","disabled");
	}
}

function editClub()
{
	var obj = $(this);

	var clubId = obj.find("input[name='clubId']").val();

	var clubName = obj.find("input[name='clubName']").val();

	obj.find("button.setting-button").attr("disabled","disabled");

	$.post("admin.php",{"do":"club_edit","clubId":clubId,"clubName":clubName},function(data)
	{
		if (data.result == "success")
		{
			if( clubId > 0 )
			{
				obj.find(".controls").attr("data",clubName);

				alertMessage("保存成功");
			}
			else
			{
				location.href = location.href;
			}
		}
		else if (data.result == "error")
		{
			obj.find("button.setting-button").removeAttr("disabled");

			alertMessage(data.message);
		}
		else
		{
			obj.find("button.setting-button").removeAttr("disabled");
			
			alertMessage("系统异常");
		}

	},"json");

	return false;
}

function trashClub()
{
	var obj = $(this);

	obj.hide();

	$.post("admin.php",{"do":"club_trash","trashId":$(this).attr("data")},function(data)
	{
		if( parseInt(data.message) == 0 )
		{
			location.href = location.pathname + "?type=trash";
		}
		else if( parseInt(data.message) == 1 )
		{
			location.href = location.pathname;
		}
		else
		{
			obj.show();

			alertMessage("系统异常");
		}
		
	},"json");
}

function saveClubOrder()
{
	var clubOrder = $("#clubList li").map(function() { return $(this).find("input[name='clubId']").val(); }).get().join(",");

	$.post("admin.php",{"do":"club_order","clubOrder":clubOrder},function(data)
	{
		if( parseInt(data.message) < 2 )
		{
			alertMessage("排序系统异常");
		}

	},"json");
}

function ordersExpire()
{
	var o = $(this);

	o.unbind("click").css({"cursor":"wait","opacity":"0.3"}).html("请稍候...");

	$.post("admin.php",{"do":"orders_expire"},function(data)
	{
		o.remove();

		if( parseInt(data.message) > 0 )
		{
			alertMessage("删除了 "+data.message+" 条过期订单");

			setTimeout("location.href='./admin_orders.php';",2000);
		}
		else
		{
			alertMessage("没有可删除订单");
		}

	},"json");	
}

function orderStatus(id,status)
{
	if( status == 4 )
	{
		if( !confirm("确定要 完成 吗？") )
		{
			return false;
		}
	}
	else if( status == 0 )
	{
		if( !confirm("确定要 关闭 吗？") )
		{
			return false;
		}
	}

	$("#status-"+id).hide();

	$.post("admin.php",{"do":"order_status","orderId":id,"orderStatus":status},function(data)
	{
		if( data.message == "0" )
		{
			$("#status-"+id).html("已关闭");
		}
		else if( data.message == "4" )
		{
			$("#status-"+id).html("已完成");
		}

		$("#status-"+id).show();

	},"json");
}

function checkGoodsInput()
{
	if( $.trim($("#goodsName").val()).length > 1 && $.trim($("#goodsFile").val()).length > 5 && parseInt($("#goodsPrice").val()) > 0 && /^[1-9]+[0-9]*]*$/.test($("#goodsPrice").val()) && $.trim($("#goodsDescription").val()).length < 255 )
	{
		$(".setting-button").removeAttr("disabled");
	}
	else
	{
		$(".setting-button").attr("disabled","disabled");
	}
}

function saveGoods()
{
	$(".setting-button").attr("disabled","disabled");

	$.post("admin.php",{"do":"store_edit","name":$("#goodsName").val(),"file":$("#goodsFile").val(),"price":$("#goodsPrice").val(),"description":$("#goodsDescription").val(),"id":$("#goodsId").val()},function(data)
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

function editGoods(gid)
{
	$("#goodsName").val($("#name_"+gid).html());

	$("#goodsFile").val($("#name_"+gid).attr("rel"));

	$("#goodsPrice").val($.trim($("#price_"+gid).html().replace("社区币","")));

	$("#goodsDescription").val($("#description_"+gid).html().replace("<br>",""));

	$("#goodsId").val(gid);

	$('button.setting-button').html("保存修改");

	$("html,body").animate({scrollTop: 0}, 200);
}

function goodsStatus()
{
	var o = $(this);

	o.hide();

	$.post("admin.php",{"do":"store_status","gid":o.attr("data")},function(data)
	{
		if (data.message == "0")
		{
			o.html("上架");
		}
		else if (data.message == "1")
		{
			o.html("下架");
		}
		else
		{
			alertMessage("系统异常");
		}

		o.show();

	},"json");
}

function updateTemplateCache()
{
	$(".setting-button").hide();

	$(".setting-end-inner").append('<img src="./static/loading.gif" style="width:29px;height:29px;border:0">');

	$.post("admin.php",{"do":"cache_delete"},function(data)
	{
		alertMessage("共清除 "+data.message+" 个模版缓存文件");

		$(".setting-end-inner img").remove();

		$(".setting-button").show();
		
	},"json");

	return false;
}