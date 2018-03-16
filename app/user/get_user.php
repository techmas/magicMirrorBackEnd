<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$accessData = $RM -> access($_REQUEST); 
$reqData = $accessData['request'];

$userData = $RM -> getUser(array('ID' => $reqData['user_id']));  

if(!empty($userData)){
	$RM -> response(
        "success", 
        1, 
        "Данные пользователя", 
        $userData
    );	
}else{	$RM -> response(
        "error", 
        0, 
        "Пользователь не найден"
    );	
}

//echo "<pre>"; print_r($userData); echo "</pre>";

/**
* @api {get} /user/get_user?user_id=**
* @apiVersion 1.0.0
* @apiName get_user 
* @apiGroup User API
*
* @apiDescription Метод возвращает данные по авторизованому пользователю.   
* Поддерживает способы передачи: GET/POST (пример для GET)    
* 
* @api {get} /user/get_user?token=***&user_id=** Получение пользователя по ID 
*
* @apiParamExample {post} Пример POST:
* /app/user/get_user/
* POST: 
* token=[value]
* user_id=[value]
* @apiParamExample {get} Пример GET: 
* /app/user/get_user?token=***&user_id=*** 
*
* @apiParam {String} token Токен пользователя
* @apiParam {String} user_id ID пользователя
*/

?>