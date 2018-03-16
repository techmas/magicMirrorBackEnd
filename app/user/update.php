<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
global $USER;
                                                                     
$accessData = $RM -> access($_REQUEST);        
$userData = $accessData['user'];
$reqData = $accessData['request'];
$error = array();

if(!empty($reqData['login'])){	
	$userLogin = $RM -> loginByPhone($reqData['login']);	
    $newData['LOGIN'] = $userLogin['login']; 
    $newData['PERSONAL_MOBILE'] = $userLogin['phone'];
}
if(!empty($reqData['password'])){	
    $newData['PASSWORD'] = $reqData['password'];
}
if(!empty($reqData['email'])){	
	if(!empty(filter_var($reqData['email'], FILTER_VALIDATE_EMAIL))){
    	$newData['EMAIL'] = $reqData['email']; 
    }else{    	$error['error'][] = 'Некорректный адрес электронной почты';	
    }
}
if(!empty($reqData['name'])){	
    $newData['NAME'] = $reqData['name'];
}
if(!empty($reqData['last_name'])){	
    $newData['LAST_NAME'] = $reqData['last_name'];
}
if(!empty($reqData['city'])){	
    $newData['PERSONAL_CITY'] = $reqData['city'];
}
if(!empty($reqData['birthday'])){	
    $newData['PERSONAL_BIRTHDAY'] = $reqData['birthday'];
}
if(!empty($reqData['gender'])){	
    $newData['PERSONAL_GENDER'] = $reqData['gender'];
}
if(!empty($reqData['is_rate'])){	
    $newData['UF_IS_RATE'] = (strtoupper($reqData['is_rate']) == 'Y') ? 1 : 0;
}
if(!empty($_FILES['avatar'])){	
	$arFile = CFile::MakeFileArray($_FILES['avatar']); 
    $arFile["del"] = "Y";
	$arFile["MODULE_ID"] = "main";
    $newData['PERSONAL_PHOTO'] = $arFile;
}

if(count($error['error']) == 0 && count($newData) > 0){

    $user = new CUser;       
	
	if($userID = $user -> Update($userData['ID'], $newData)){   

		// Запрашиваем все данные
		$userAccess = $RM -> access($_REQUEST);
		
		$RM -> response(
			'success', // Текстовый код ответа
			'1',       // Цифровой код ответа
			'Профиль успешно изменен', // Статус ответа
			$userAccess['user']  // Данные ответа   
		);	
	}else{  
		$error['error'] = explode('<br>', $user->LAST_ERROR);
		$RM -> response(
	        "error", 
	        0, 
	        "Ошибка изменения профиля",
	        $error
	    );
	}

}else{
	$RM -> response(
        "error", 
        0, 
        "Отсутствуют данные для обновления",  
        $error
    );
}

//echo '<pre>'.print_r($newUser, true).'</pre><br>'; // Debug 

/**
* @api {method} / Изменение профиля
* @apiVersion 1.0.0
* @apiName update
* @apiGroup User API
*
* @apiDescription Изменяет данные профиля пользователя. В качестве логина используется очищенный от артефактов номер телефона 
*
* Поддерживает способы передачи: POST 
*
* *- поля, обязательные для заполнения   
*
*
* @api {post} /user/update/ Изменения профиля пользователя
*
* @apiParamExample {post} Пример POST:
* /app/user/update/
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
* @apiParam {String} token Токен пользователя
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
*/

?>