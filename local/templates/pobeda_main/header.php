<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?$APPLICATION->ShowTitle();?></title>
	<meta name=description content="">
    
	<meta name=viewport content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH ;?>/favicon.ico" />

    <?$APPLICATION->ShowHead();?>
    <?CJSCore::Init(array('ajax', 'popup'));?>
</head>
<body>
<?
CPullWatch::Add($USER->GetID(), 'pobeda'); 
$arUser = CUser::GetByID($USER->GetID()) -> Fetch();
$icon = (!empty($arUser['PERSONAL_PHOTO'])) ? CFile::GetPath($arUser['PERSONAL_PHOTO']) : '/lib/feya.jpg';  
$user_id = (!empty($USER->GetID())) ? $USER->GetID() : $_COOKIE['BITRIX_SM_GUEST_ID'];
?>


<script type="text/javascript">
// Подтверждение приема уведомляшек
Notification.requestPermission(function(permission){
// переменная permission содержит результат запроса
console.log('Результат запроса прав:', permission);
});

// Отлов пушей и вывод в уведомляху
BX.ready(function(){
	BX.addCustomEvent("onPullEvent", function(module_id, command, params) {  
		
		if (module_id == "personal" && command == 'sand'){ 			var notification = new Notification(
		    	params.title, { body: params.msg, dir: 'auto', icon: params.icon }
		    );
		}
	});
	BX.PULL.extendWatch('pobeda');
});
//BX.PULL.getDebugInfo();
</script>