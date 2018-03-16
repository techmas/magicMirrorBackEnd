<?

define("PULL_AJAX_INIT", true);
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);

if (!CModule::IncludeModule("pull"))
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'PULL_MODULE_IS_NOT_INSTALLED'));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
	die();
}
/*if (intval($USER->GetID()) <= 0)
{
	echo CUtil::PhpToJsObject(Array('ERROR' => 'AUTHORIZE_ERROR'));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
	die();
}*/

/*$arMessages[] = array(
         "USER_ID" => $value['user_id'],
         "TITLE" => 'Push test script',
         "APP_ID"=> $value['app_id'],
         "MESSAGE" => $request['message'],
      );
$pushMe = new CPushManager();
$result = $pushMe->SendMessage($arMessages);*/

/*CPullWatch::Add($USER->GetId(), 'pobeda');

CPullStack::AddShared(
   Array(
      'module_id' => 'test',
      'command' => 'check',
      'params' => Array('user' => $USER->GetFullName(), 'msg' => $_REQUEST['msg']),
   )
);
*/  

$userName = ($USER->GetID()) ? $USER->GetFullName() : 'Аноним #'.$_COOKIE['BITRIX_SM_GUEST_ID'];
$user_id = (!empty($USER->GetID())) ? $USER->GetID() : $_COOKIE['BITRIX_SM_GUEST_ID'];
CPullWatch::AddToStack('pobeda',
	Array(
		'module_id' => 'test',
		'command' => 'check',
		'params' => Array('user_id' => $user_id, 'user' => $userName, 'msg' => $_REQUEST['msg'], "icon" => $_REQUEST['icon'])  
	)
);
                         
/*CPullStack::AddByUser(
   -148, 
   Array(
       'module_id' => 'test',
       'command' => 'check',
       'params' => Array('user' => $userName, 'msg' => $_REQUEST['msg'], "icon" => $_REQUEST['icon']),
   )
);*/


/*if (check_bitrix_sessid())
{
	if ($_POST['SEND'] == 'Y')
	{
		CPullWatch::AddToStack('pobeda',
			Array(
				'module_id' => 'test',
				'command' => 'check',
				'params' => Array("TIME" => date('d.m.Y H:i:s').' '.$USER->GetFullName().' &mdash; ну, отправил и что дальше?')
			)
		); 
		echo CUtil::PhpToJsObject(Array('ERROR' => ''));
	}
	else
	{
		echo CUtil::PhpToJsObject(Array('ERROR' => 'UNKNOWN_ERROR'));
	}
}
else
{
	echo CUtil::PhpToJsObject(Array(
		'BITRIX_SESSID' => bitrix_sessid(),
		'ERROR' => 'SESSION_ERROR'
	));
}*/
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>