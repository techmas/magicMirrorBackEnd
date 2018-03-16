<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$userData = $RM -> getRequest($_REQUEST); 
$error = array();

if(empty($userData['name'])){
    $error['error'][] = 'Введите имя';	
}

if(empty($userData['last_name'])){
    $error['error'][] = 'Введите фамилию';	
}

if(empty($userData['login'])){	
    $error['error'][] = 'Введите логин (номер телефона)';	
}else{
	$userLogin = $RM -> loginByPhone($userData['login']);	
}

if(empty($userData['password'])){
    $error['error'][] = 'Введите пароль';	
}elseif(strlen(trim($userData['password'])) < 6){
	$error['error'][] = 'Пароль должен быть не меньше 6-ти символов';
}

if(empty($userData['email']) || empty(filter_var($userData['email'], FILTER_VALIDATE_EMAIL))){ 
	$error['error'][] = 'Некорректный адрес эл. почты';
}


// Обновляем токен устройства (если подразумеваем рассылку пушей)
/*$dataDeviceToken = array();

if($reqData["device_token"]){
	
	if(!empty($reqData["device_id"]) && !empty($reqData["device_type"]) && !empty($reqData["app_id"])){  
	
		$dataDeviceToken = array(
	        "DEVICE_TOKEN" => $reqData["device_token"],
	        "DEVICE_ID" => $reqData["device_id"],
	        "DEVICE_TYPE" => $reqData["device_type"],
	        "APP_ID" => $reqData["app_id"],
	        "DATE_AUTH" => ConvertTimeStamp(getmicrotime(), "FULL")
	    ); 
    }else{
    	$error['error'][] = 'Недостаточно данных для установки токена устройства';	
    }
}*/

if(count($error) == 0){

    $user = new CUser;    
    $arFile = '';
    
    if(!empty($_FILES['photo'])){
	    $arFile = CFile::MakeFileArray($_FILES['avatar']); 
	    $arFile["del"] = "Y";
		$arFile["MODULE_ID"] = "main";
	}
 
    
	$arFields = Array(
		"EMAIL"             => $userData['email'],
		"LOGIN"             => $userLogin['login'], 
		"LID"               => "ru",
		"ACTIVE"            => "Y",
		"GROUP_ID"          => array(5), 
		"PASSWORD"          => $userData['password'],
        "NAME"              => $userData['name'], 
        "LAST_NAME"         => $userData['last_name'],  
        "PERSONAL_MOBILE"   => $userData['phone'],
        "PERSONAL_GENDER"   => $userData['gender'],
        "PERSONAL_BIRTHDAY" => $userData['birthday'],
        "PERSONAL_CITY" 	=> $userData['city'],  
        "PERSONAL_PHOTO" 	=> $arFile, 
        "UF_IS_RATE" 		=> (strtoupper($userData['is_rate']) == 'Y') ? 1 : 0; 
	);  
	
	if($userID = $user -> Add($arFields)){ 
	    
		/*
		$affilComment = '';  
	    if(!empty($userData['affiliate'])){
	    	
	    	$affiliateData = $RM::getUser(array("LOGIN" => $RM::loginByPhone($userData['affiliate'])));
	    	$affilComment = "Новый пассажир от ".$affiliateData['NAME'].' '.$affiliateData['LAST_NAME'];
	    }
	    
		// Регистрация аффилиата
	    $affiliateAdd = array(
		    'SITE_ID' => 's1',
		    'USER_ID' => $userID,
		    'AFFILIATE_ID' => $affiliateData['PARTNER']['ID'],
		    'PLAN_ID' => 1,
		    'ACTIVE' => 'Y',
		    'DATE_CREATE' => date('d.m.Y H:i:s'),
		    'AFF_DESCRIPTION' => $affilComment,
		    'FIX_PLAN' => 'N' 
		);

		CSaleAffiliate::Add($affiliateAdd); 
		*/
		
		if($ex = $APPLICATION->GetException()){			
		   	$error['error'] = explode('<br>', $strError = $ex->GetString());
		   	$RM -> response(
		        "error", 
		        0, 
		        "Ошибка регистрации",
		        $error
		    );	
		}else{
			
			// Ставим токен
			$token = $RM::setToken($userID); 
			// Ставим токен устройства  
			if(!empty($dataDeviceToken)){
				$dataDeviceToken['USER_ID'] = $userID;
				$deviceToken = $RM::setDeviceToken($dataDeviceToken);
			}
			// Запрашиваем все данные
			$userAccess = $RM -> access(array('token' => $token));  
	
			$RM -> response(
				'success', // Текстовый код ответа
				'1',       // Цифровой код ответа
				'Пользователь авторизован', // Статус ответа
				$userAccess['user']  // Данные ответа
			);
		}	
	}else{  
		$error['error'] = explode('<br>', $user->LAST_ERROR);
		$RM -> response(
	        "error", 
	        0, 
	        "Ошибка регистрации",
	        $error
	    );
	}

}else{
	$RM -> response(
        "error", 
        0, 
        "Ошибка регистрации",
        $error
    );
}

//echo '<pre>'.print_r($newUser, true).'</pre><br>'; // Debug 

/**
* @api {method} / Регистрация клиента
* @apiVersion 1.0.0
* @apiName reg
* @apiGroup User API
*
* @apiDescription Регистрирует пользователя. В качестве логина используется очищенный от артефактов номер телефона 
* Поддерживает способы передачи: POST 
* * — поля, обязательные для заполнения
*
* @api {post} /user/reg/ Регистрация
*
* @apiParamExample {post} Пример POST:
* /app/user/reg/
* POST: 
* login = [string]
* password = [string]	
* email = [string]
* name = [string]	
* last_name = [string]	
* city = [string]
* birthday = [string]
* gender = [string]
* is_rate = [string]
* avatar = [file]	 	
*
* @apiParam {String} login Логин*  Он же - номер телефона. Можно вводить в произвольном формате(+7 или 8, скобочки, тире, пробелы и пр.), значение имеет только правильный набор цифр
* @apiParam {String} password Пароль*
* @apiParam {String} email Электронный адрес*  
* @apiParam {String} name Имя *
* @apiParam {String} last_name Фамилия *
* @apiParam {String} city Город
* @apiParam {String} birthday День рождения
* @apiParam {String} gender Пол [M - мужчина; F - женщина] 
* @apiParam {String} is_rate Согласие на участие в рейтингах [Y - да, N - нет]
* @apiParam {File} avatar Аватарка
* 
*/

?>