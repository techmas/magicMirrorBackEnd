<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CPullWatch::Add($USER->GetID(), 'pobeda'); 
$arUser = CUser::GetByID($USER->GetID()) -> Fetch();
$icon = (!empty($arUser['PERSONAL_PHOTO'])) ? CFile::GetPath($arUser['PERSONAL_PHOTO']) : '/lib/feya.jpg';  
$user_id = (!empty($USER->GetID())) ? $USER->GetID() : $_COOKIE['BITRIX_SM_GUEST_ID'];
?>
	<img src="<?=$icon?>" align="left" style="padding:0 30px 0 0;" width="150" alt="" border="0" >
	<h3>Общий чат</h3>
	<div style="float:left;width:80%;">
		<div style="float:left;width:50%;height:100%;;" id="pull_test"></div>

		<div style="clear:both;"></div>
		<textarea id="msg" name="msg" style="width:49%;heoght:100px;padding:5px;margin:10px 0 5px 0;"></textarea><br />
		<button id="subscribe" onclick="sendCommand();">Зафигачить!</button>
	</div>


<script type="text/javascript">

Notification.requestPermission(function(permission){
// переменная permission содержит результат запроса
console.log('Результат запроса прав:', permission);  
});

function sendCommand()
{  
	BX.ajax({
		url: '/local/components/bitrix/pull.test/ajax.php',
		method: 'POST',
		data: {'SEND' : 'Y', 'msg': $('#msg').val(), 'icon': '<?=$icon?>', 'sessid': BX.bitrix_sessid()}
	});
}

BX.ready(function(){
	BX.addCustomEvent("onPullEvent", function(module_id, command, params) {  
		
		if (module_id == "test" && command == 'check')
		{  
			BX('pull_test').innerHTML += '<div style="height:100%;padding:5px;min-height:50px;border:1px dotted #E2E2E2;margin:10px 0;"><img src="'+params.icon+'" align="left" style="padding:0 5px;" width="48" height="48" alt="" border="0"><b>'+params.user+'</b><br>'+params.msg+'</div><div style="clear:both;"></div>';
			$('#msg').val(''); 
			if (/*Notification.permission === "granted" && */ params.user_id != '<?=$user_id?>') {
			    // If it's okay let's create a notification
			    var notification = new Notification(
			    	params.user,
			    	{ body: params.msg, dir: 'auto', icon: params.icon}
			    );
			  }
		}
	});
	BX.PULL.extendWatch('pobeda');
});
//BX.PULL.getDebugInfo();
</script>
