<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;

$userID = $_REQUEST['user_id'];
$checkword = $_REQUEST['checkword'];
$company_name = htmlspecialchars(strip_tags($_REQUEST['company_name']));
$password = htmlspecialchars(strip_tags($_REQUEST['password']));
$email = htmlspecialchars(strip_tags($_REQUEST['email']));


//$arUser = CUser::getByID(130)->Fetch(); 
//echo '<pre>'.print_r($arUser, true).'</pre><br>'; // Debug 

$filter = array("CHECKWORD" => $checkword, "ACTIVE" => "Y");

if(!empty($checkword) && $arUser = CUser::GetList(($by="id"), ($order="desc"), $filter)->Fetch()){ 

    if(!empty($_FILES['avatar']['size']) && is_array($_FILES['avatar'])){     
        $fieldsArr["PERSONAL_PHOTO"] = CFile::MakeFileArray(CFile::SaveFile($_FILES['avatar']));	
    }
    if(!empty($_REQUEST['email'])){
    	$fieldsArr["EMAIL"] = $email;
    } 
    if(!empty($_REQUEST['password'])){
    	$fieldsArr["PASSWORD"] = $password; 
    	$fieldsArr["CONFIRM_PASSWORD"] = $password;   
    } 
    if(!empty($_REQUEST['phone'])){
    	$fieldsArr["LOGIN"] = loginByPhone($_REQUEST['phone']);; 
    	$fieldsArr["PERSONAL_PHONE"] = $_REQUEST['phone'];   
    }
         
    $user = new CUser;
    $newUserData = $user->Update(
        $arUser['ID'], 
        $fieldsArr
    );  
    
    $arAuthResult = $USER->Login($arUser['LOGIN'], $password, "Y");
    //$APPLICATION->arAuthResult = $arAuthResult;  
    //echo $strError .= $user->LAST_ERROR;   
    
    $userID = $arUser['ID'];
    unset($arUser);
    $arUser = CUser::getByID($userID)->Fetch();   
     
    $rootFolder = CIBlockSection::GetList(
        Array(), 
        array(
            'IBLOCK_CODE' => 'albums',
            'CODE' => 'USER_'.$arUser['ID'],
        )
    ) -> fetch();
    
    $setAlbum = new CIBlockSection;
    $arFields = Array(  
        "ACTIVE" => 'Y',     
        "NAME" => $arUser['NAME'].' '.$arUser['LAST_NAME'].'/'.$arUser['LOGIN']    
    );
    $setAlbum -> Update($rootFolder['ID'], $arFields)
    
    $arUser['BASE_FOLDER'] = 'USER_'.$arUser['ID'];
    $arUser['MAIN_ALBUM'] = 'MAIN_'.$arUser['ID'];
	
	returnJSON(
        "success", 
        1, 
        "Данные пользователя", 
        $arUser
    );

}else{
    print json_encode(
		array(
		    "status"      => "error",
		    "status_code" => "0",
			"status_msg"  => "Пользователь не найден"
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
}