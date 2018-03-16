<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
 
//$headers = get_headers((!empty(CMain::IsHTTPS()) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 1);
//echo '<pre>'.print_r($_REQUEST, true).'</pre><br>'; // Debug 

$userData = $RM -> access($_REQUEST);  
 
// Если ошибок нет - выдаем данные пользователя.       
$RM -> response(
	'success', // Текстовый код ответа
	'1',       // Цифровой код ответа
	'Пользователь авторизован', // Статус ответа  
	$userData['user']  // Данные ответа
); 

/**
* @api {method} / Проверка авторизации
* @apiVersion 1.0.0
* @apiName is_logged 
* @apiGroup User API
*
* @apiDescription Проверяет, авторизован ли пользователь, если нет - авторизует по токену, возвращает данные пользователя
*
* Технически, проводится в каждом запросе, который требует авторизации. При передаче правильного токена логинит автоматом.
*
* Поддерживает способы передачи: GET/POST (пример для GET)    
*
* @api {get} /user/is_logged?token=****** Проверка авторизации 
*
* @apiParamExample {post} Пример POST:
* /app/user/is_logged/
* POST: token=[value]
* @apiParamExample {get} Пример GET: 
* /app/user/is_logged?token=*** 
*
* @apiParam {String} token Токен пользователя
*/