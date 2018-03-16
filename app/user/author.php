<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;

unset($authorUser['error']);

if(!empty($_REQUEST['login'])){
	$authorUser['login'] = htmlspecialchars(strip_tags($_REQUEST['login']));
}elseif(!empty($_REQUEST['phone'])){
	$authorUser['login'] = loginByPhone($_REQUEST['phone']);
}else{
    $authorUser['error'][] = 'Введите логин (номер телефона)';	
}

if(!empty($_REQUEST['password'])){
	$authorUser['password'] = htmlspecialchars(strip_tags($_REQUEST['password']));
}else{
    $authorUser['error'][] = 'Введите пароль';	
}
                                                                  
if(count($authorUser['error']) == 0){

    if(!$USER->IsAuthorized()){    	        $loginResult = $USER -> Login($authorUser['login'], $authorUser['password'], "Y");  
           
        //echo '<pre>'.print_r($loginResult, true).'</pre><br>'; // Debug   
        
        if($loginResult['TYPE'] != 'ERROR'){ 
           
	        $userData = CUser::getByID($USER->GetID())->Fetch();  
	        if($token = $RM::setToken($userData['ID'])){ 	        
	        	$arUser = $RM -> access(array('token' => $token));  
	        }
	        
	        $RM -> response(
		        "success", 
		        1, 
		        "Данные пользователя", 
		        $arUser['user']
		    );
		}else{
			$RM -> response(
		        "error", 
		        0, 
		        "Пользователь не найден",
		        $USER->LAST_ERROR 
		    );                           
		}
        
    }else{  
        
    	$loginResult = $USER -> Login($authorUser['login'], $authorUser['password'], "Y"); 
    	if($loginResult['TYPE'] != 'ERROR'){    
    	
	        $userData = CUser::getByID($USER->GetID())->Fetch();       
		    $arUser = $RM -> access(array('token' => $userData['UF_TOKEN'])); 
	    	    	$RM -> response(
		        "notice", 
		        1, 
		        "Пользователь авторизован v2",
		        $arUser['user']
		    ); 
	    }else{
			$RM -> response(
		        "error", 
		        0, 
		        "Пользователь не найден",
		        $USER->LAST_ERROR 
		    );                           
		}
    }	
}else{
	$RM -> response(
        "error", 
        0, 
        "Не указаны логин или пароль",
        $authorUser
    );	
}

/**
 * @api {get} /user/author?login=12334545&password=****** Авторизация
 * @apiVersion 1.0.0
 * @apiName author 
 * @apiGroup User API
 *
 * @apiDescription Метод возвращает данные по авторизованому пользователю.   
 * Поддерживает способы передачи: GET/POST (пример для GET)    
 * \* &mdash; поля, обязательные для заполнения
 * 
 * @apiParam {String} login Логин* учётной записи
 * @apiParam {String} password Пароль* учётной записи 
 */

?>