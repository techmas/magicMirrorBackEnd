<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;

$userID = $_REQUEST['user_id'];
$checkword = trim($_REQUEST['checkword']);

$filter = array("CHECKWORD" => $checkword, "ACTIVE" => "Y");


if($rsUsers = CUser::GetList(($by="ID"), ($order="desc"), $filter) -> fetch()){ 
    //echo '<pre>'.print_r($rsUsers, true).'</pre><br>'; // Debug     
    
    $rsUsers['BASE_FOLDER'] = 'USER_'.$rsUsers['ID'];
    $rsUsers['MAIN_ALBUM'] = 'MAIN_'.$rsUsers['ID'];
    	if(!empty($rsUsers)){		
		returnJSON(
	        "success", 
	        1, 
	        "Данные пользователя", 
	        $rsUsers
	    );	    
	}
}else{
    returnJSON(
        "error", 
        0, 
        "Пользователь не найден" 
    );
}