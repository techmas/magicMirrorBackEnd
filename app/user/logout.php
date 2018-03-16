<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;

$userData = $RM -> access($_REQUEST);

$RM::clearToken($userData['user']['ID']);  

if(!empty($userData['user']['UF_TOKEN'])){	   
	// Разлогиниваем, сносим токен	
	$USER->Logout(); 
	         
	$RM -> response(
		'success', // Текстовый код ответа
		'1',       // Цифровой код ответа
		'Пользователь вышел'
	); 			
}else{
	$RM -> response(
		'error', // Текстовый код ответа
		'0',       // Цифровой код ответа
		'Пользователь не авторизован'
	);
}

//echo '<pre>'.print_r($userData, true).'</pre><br>'; // Debug 

/**
* @api {method} / Выход (разлогинивание)
* @apiVersion 1.0.0
* @apiName logout 
* @apiGroup User API
*
* @apiDescription Разлогинивает авторизованного пользователя
*
* Поддерживает способы передачи: GET/POST (пример для GET)    
*
* @api {get} /user/logout?token=****** Выход (разлогинивание)
*
* @apiParamExample {post} Пример POST:
* /app/user/logout/
* POST: token=[value]
* @apiParamExample {get} Пример GET: 
* /app/user/logout?token=*** 
*
* @apiParam {String} token Токен пользователя
*/
?>