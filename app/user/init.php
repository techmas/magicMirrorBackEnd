<?
/*
Мурзилка по RestMain.
****По умолчанию определен в /local/php_interface/init.php, как $RM
	example: 
		$RM -> access (token[var], permission[array]);	

1. public access (token[var]/REQUEST[array], permission[array])
    ---------------------------------------------------------------
	| WARNING!!! / ACHTUNG!!! / ALARM!!!                          |
	| В пользовательских свойствах юзверя должна быть определена  |
	| переменная UF_TOKEN!!!                                      |
	---------------------------------------------------------------
	Проверяет доступ: если есть правильный токен - получим данные юзверя (при необходимости перелогинит), 
	если нет - отправит ошибку, тормознет сценарий
	Первая переменная: если передаем весь массив REQUEST(POST, GET)(содержащий переменную token) - вернет данные пользователя + рекурсивно обработает все переменные запроса (urldecode, trim, htmlspecialchars, strip_tags) 
	example: 
		data(
			"user" => Array(*user_data*),
			"request" => Array(*request_data*)
		)
	Если передаем отдельную переменную token - просто вернет данные пользователя   
	example:
		data(
			Array(*user_data*)
		)
	Вторая переменная (необязательная) - массив СИМВОЛЬНЫХ КОДОВ(!!!) групп пользователей [array('open' => array('client'), 'close' => array('driver'))], которым разрешен или закрыт доступ. Можно передавать оба массива, можно какой-то один
	Для админов доступ всегда и везде, по умолчанию - всем.    
	
2. public response (status[string], status_code[var], status_msg[string], dataArr[array])
	Выводит ответ в формате JSON
	Перед выводом, рекурсивно обрабатывает массив dataArr, выкидывая все пустые переменные.
	При необходимости, из UF_TOKEN делает переменную TOKEN
	Выводит заголовки application/json
	Конвертирует ответ из PHP-массива в JSON   
	
3. public static getRequest (REQUEST/GET/POST[array])
	рекурсивно обработает все переменные запроса (urldecode, trim, htmlspecialchars, strip_tags), вернет массив, сохранив структуру     
	
4. 5. 6... Получение групп, создание токена, обработка телефонов, алиасы из заголовков и пр. - смотреть по коду
*/

class RestMain{ 

	// Получение всех пользовательских групп
	public static function getGroup(){

		$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array('ACTIVE' => 'Y'));
		while($rsGroupsArr = $rsGroups -> fetch()){
		
			$groupArr[$rsGroupsArr['ID']] = $rsGroupsArr; 
		}	
	
		return $groupArr; 
	}

	// Получение групп пользователя по USER_ID
	public static function getUserGroup($userID){
/*
		$allGroup = self::getGroup();
		$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array('ACTIVE' => 'Y'));
		$userGroup = CUser::GetUserGroupArray($arUser['ID']);
		AddMessage2Log(serialize($userGroup), "userGroup");
		foreach($userGroup as $id){
			$arUser[$allGroup[$id]['ID']] = $allGroup[$id]['STRING_ID'];
		}
*/

		$allGroup = self::getGroup();
		$userGroup = CUser::GetUserGroup($userID);
		//AddMessage2Log(serialize($userGroup), "userGroup");
		foreach($userGroup as $id)
		{
			if(array_key_exists($id, $allGroup) )
			{
				$arUser[$allGroup[$id]['ID']] = $allGroup[$id]['STRING_ID'];
			}
		}

		return  $arUser;
	} 
	
	// Получение всех аффилиатов
	public static function allAffiliates(){
		$affiliateArrQ = CSaleAffiliate::GetList(array(), array('!ID' => false));  
		while($affiliateArr = $affiliateArrQ -> fetch()){
	
			$affilFullArr[$affiliateArr['USER_ID']] = $affiliateArr; 
		}
		
		return $affilFullArr;	
	}
	
	// Рекурсивная проверка афиилиатов для пирамиды
	public static function affiliatePyramid(&$affiliateArr, $affiliateID = '', $level = 0, $returnArr = ''){
	
		++$level; 

		foreach($affiliateArr as $affData){

			if($affData['ID'] == $affiliateID && $level <= 3){ 

				$returnArr[$level] = $affiliateArr[$affData['USER_ID']];  
				$returnArr = affiliatePyramid($affiliateArr, $affData['AFFILIATE_ID'],  $level, $returnArr);   
			}		
		}
	
		return $returnArr;
	}  
	
	// Получение аффилиатов
	public static function getAffiliat($userID){ 
	     
		if(!empty($userID) && class_exists('CSaleAffiliate')){
			return $affiliate = CSaleAffiliate::GetList(array(), array('USER_ID' => $userID)) -> fetch(); 
		}
	} 
	
	// Получение устройств привязанных к юзверю
	public static function getDeviceData($userID){ 
	     
		if(!empty($userID) && class_exists('CPullPush')){
			$allUserDevice = array();
			$arDeviceQ = CPullPush::GetList(Array(), Array("USER_ID" => $userID));
			while($arDevice = $arDeviceQ -> fetch()){
				$allUserDevice[] = $arDevice;	
			} 
	
			if($allUserDevice){
				return 	$allUserDevice;
			}else{
				return 	false;
			}	
		}
	} 
	  
	// Получение данных юзверя по фильтру
	public static function getUser($filter){
        
		if(!empty($filter)){    
	
			$filter["ACTIVE"] = "Y";  
			$arUser = CUser::GetList(($by="id"), ($order="desc"), $filter, array('SELECT' => array('UF_*'))) -> Fetch();     
        
	  		if(!empty($arUser['ID'])){ 
				$arUser['USER_GROUP'] = self::getUserGroup($arUser['ID']); 
				$arUser['PARTNER'] = self::getAffiliat($arUser['ID']);
				$arUser['DEVICES'] = self::getDeviceData($arUser['ID']);
			}else{
				unset($arUser);
			}
		}
	
		return (!empty($arUser)) ? $arUser : false;
	}

	// Проверка пользователя по токену
	public function checkUser($token){
        
		if(!empty($token)){    
	
			$filter = array("UF_TOKEN" => $token, "ACTIVE" => "Y");  
			$arUser = CUser::GetList(($by="id"), ($order="desc"), $filter, array('SELECT' => array('UF_*'))) -> Fetch();     
        
	  		if(!empty($arUser['ID']) &&  strnatcmp($arUser['UF_TOKEN'], $token) == false){ 
				$arUser['USER_GROUP'] = self::getUserGroup($arUser['ID']); 
				$arUser['PARTNER'] = self::getAffiliat($arUser['ID']);
				$arUser['DEVICES'] = self::getDeviceData($arUser['ID']);   
			}else{
				unset($arUser);
			}
		}
	
		return (!empty($arUser)) ? $arUser : false;
	}

	// Рекурсивно обходим массив и выкидываем все пустые значения
	private static function checkData($dataArr){

		$dataParam = array();
		
		foreach($dataArr as $key => $dataParam){ 
	
			if(is_array($dataParam) && count($dataParam) > 0){
			
				$return[$key] = self::checkData($dataParam);
			
			}else{  
		    
				if(!empty($dataParam) && !preg_match('/VALUE_ID$|ENUM_ID$/', $key)){ 
			        
			 		$return[$key] = trim($dataParam);
			 		  
			 		if(!is_numeric($key)){ 
				 		if($key == 'PERSONAL_PHOTO'){  
					    	// Заменяем ID фотки на абсолютную ссылку  			     
					    	$return[$key] = (!empty(CMain::IsHTTPS()) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].CFile::GetPath($dataParam);
					    }
					    elseif($key == 'UF_TOKEN'){    
					    	// Подпихиваем UF_TOKEN в знакомый TOKEN		     
					    	$return['TOKEN'] = trim($dataParam);
					    	unset($return['UF_TOKEN']);	
					    	 
					    }elseif($key == 'UF_RATE'){     
					    	// Заменяем код рейтинга - значением
					    	$rateArr = CUserFieldEnum::GetList(array(), array("ID" => $dataParam)) -> fetch();			     
					    	$return['UF_RATE'] = $rateArr['VALUE'];
					    } 
				    }
				}
			}
		}	
	
		return $return;
	}

	// Вывод ответа в формате JSON
	public function response($status, $status_code, $status_msg, $dataArr = array()){
    
	    $response = self::checkData($dataArr);    
    
	    $responseArr = array( 
		    "status"      => $status,
		    "status_code" => $status_code,
			"status_msg"  => $status_msg,		
	    ); 
	    if(!empty($dataArr['error'])) {
	    	$responseArr["error_msg"] = array_diff($dataArr['error'], array('')); 
	    }else{  
	    	if(!empty($response)) $responseArr["data"] = $response;
	    }	
         
	    header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers', 'Content-Type');
		header('Content-Type: application/json; charset=utf-8');
  
	    echo json_encode($responseArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 	
	}

	// Очистка телефона от артефактов
	public static function loginByPhone($phone){  
	
		$login = preg_replace('/[^0-9]+/', '', $phone);
		$login = preg_replace('/^[8|7]/', '', $login);
	    $phNun = $login;
    	$phoneNice = '+7('.$phNun[0].''.$phNun[1].''.$phNun[2].')'.$phNun[3].''.$phNun[4].''.$phNun[5].'-'.$phNun[6].''.$phNun[7].'-'.$phNun[8].''.$phNun[9];
		return array('login' => $login, 'phone' => $phoneNice);
	}

	// Генерация случайной строки
	public static function rndString($num = 7)
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randstring = '';
	    for ($i = 0; $i <= $num; $i++) {
	        $randstring .= $characters[rand(0, strlen($characters))];
	    }
	    return $randstring;
	}

	// Корректная транcлитерация для ЧПУ
	public static function replaceAlias($msg){

	    $rus = array(' ','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О',
	                'П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','а','б',
	                'в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у',
	                'ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я');

	    $lat = array('_','a','b','v','g','d','e','jo','zh','z','i','j','k','l','m','n','o',
	                'p','r','s','t','u','f','h','c','ch','sh','shh','', 'y','', 'je','ju','ja','a',
	                'b','v','g','d','e','jo','zh','z','i','j','k','l','m','n','o','p','r','s','t',
	                'u','f','h','c','ch','sh','shh','', 'y','', 'je','ju','ja'); 

	    $msg = strtolower(str_replace( $rus, $lat, $msg )); 
	    $msg = preg_replace('/[^\-_а-яА-Яa-zA-Z0-9]/', '', $msg);

	    return $msg;
	}  
	
	// Рекурсивно обходим массив полученных переменных, декодируем, чистим от потенциального хлама, возвращаем
	public static function getRequest($dataArr){

		foreach($dataArr as $key => $dataParam){ 
	
			if(is_array($dataParam) && count($dataParam) > 0){
			
				$return[$key] = self::getRequest($dataParam);
			
			}else{  
		    
  			   $return[$key] = htmlspecialchars(strip_tags(trim(urldecode($dataParam))));	 						
			}
		}	
	
		return $return; 
	} 
	
	// Установка токена
	public static function setToken($userID){ 
	
		// Ставим новый токен
      	$user = new CUser;
		$fields = Array( 
			"UF_TOKEN" => self::rndString(40), 
		); 
		if($user -> Update($userID, $fields)){
			return $fields["UF_TOKEN"]; 
		}else{
			return false;	
		} 	
	}
	
	// Удаление токена
	public static function clearToken($userID){ 
	
      	$user = new CUser;
		$fields = Array( 
			"UF_TOKEN" => '', 
		); 
		if($user -> Update($userID, $fields)){
			return $fields["UF_TOKEN"]; 
		}else{
			return false;	
		} 	
	}  
	
	// Установка токена для пушей
	public static function setDeviceToken($data){ 
	   
		global $APPLICATION;
	 
	    if(!empty($data)){
	    	
			$arToken = CPullPush::GetList(Array(), Array("DEVICE_ID" => $data["DEVICE_ID"])) -> Fetch();
		    $status = "failed";

		    $status = "failed";

		    if($arToken["ID"]){ 
		    
		    	$status = "Update";
		        CPullPush::Update($arToken["ID"], $data);
		        $status = "updated";
		    }else{ 
		    	$status = "Add";   
		    	// Фикса для бага сохранения типа устройства: при создании плюётся от всего, кроме GOOGLE, но обновить можно на что угодно!
		    	if($data['DEVICE_TYPE'] == 'APPLE'){ 
		    		$data['DEVICE_TYPE'] = 'GOOGLE';
		    		if ($resApp = CPullPush::Add($data)){  
		    			$data['DEVICE_TYPE'] = 'APPLE';
		    		    $appleFix = CPullPush::GetList(Array(), Array("ID" => $resApp)) -> Fetch();
			            CPullPush::Update($appleFix["ID"], $data);
			        } 
			        
			        $status = "registered Facking Ogryzok!";
		    	}else{
			        if ($res = CPullPush::Add($data)){
			            $status = "registered";
			        }
		        }
		    }    
		    
		    return $status;
		    
	    }else{
	    	return false;	
	    }	
	}

	// Проверка доступа пользователя к сценарию
	public function access($request = '', $permission = array()){  
	    
		global $USER; 
		
		// Если токена нет в REQUEST - ищем в HEADER
        if(empty($request['token'])){
        	//$headers = get_headers((!empty(CMain::IsHTTPS()) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 1);
        	$headers = getallheaders();
        	$request['token'] = trim($headers['Authorization']);      
        } 
		
		// Продолжаем поиски токена...
		if(is_array($request) && count($request) > 1){
        	$reqData = self::getRequest($request);
        }else{
        	$reqData['token'] = (!empty($request['token'])) ? $request['token'] : $request;  
        	$reqData['token'] = htmlspecialchars(strip_tags(trim(urldecode($reqData['token']))));  
        }  
        
        // Если есть токен - запрашиваем данные
        if(!empty($reqData['token'])){         
			$arUser = $this -> checkUser($reqData['token']); 
		}  
	    
		// Если токена нет или он левый - возвращаем ошибку, тормозим сценарий
		if(empty($arUser)){			
			$this -> response(
		    	'error', 
		    	'0',     
		    	'Пользователь не авторизован, изменил учетные данные или заблокирован'/* (кривой токен); error_token: '.$reqData['token']*/  
		    ); 
	 
		    die();	    	
		}
		// Проверяем права на доступ к сценарию для групп пользователей
		elseif(!empty($permission) &&	 
			!in_array('admin', $arUser['USER_GROUP']) && 
			(
				(!empty($permission['open']) && count(array_intersect($arUser['USER_GROUP'], $permission['open'])) == 0) ||
				(!empty($permission['close']) && count(array_intersect($arUser['USER_GROUP'], $permission['close'])) != 0)
			)
		){
			//AddMessage2Log(serialize($permission), "permission");
			//AddMessage2Log(serialize($arUser['USER_GROUP']), "arUser");
		    //$inter = array_intersect($arUser['USER_GROUP'], $permission['open']);
			//echo '<pre>'.print_r($inter, true).'</pre><br>'; // Debug
				
			$this -> response(
		    	'error', 
		    	'0',     
		    	'Недостаточно прав для доступа; error_token: '.$reqData['token'] 
		    );
		    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/app/user/token.txt', 'Permission filed '.date('d.m.Y H:i:s').': '.$reqData['token']."\r\n", FILE_APPEND | LOCK_EX);    
		    die();	    
		}
		// Если все ОК - выдаем данные юзверя
		else{  			 
			// Если разлогинен - авторизуем
			if(!$USER->IsAuthorized()){
	        	if($USER -> Authorize($arUser['ID'])){     
	        		$arUser = $this -> checkUser($arUser["UF_TOKEN"]); 
	        	}                                                     	        	
	        }        
	        
	        if(is_array($reqData) && count($reqData) > 0){
	        	 $currData['request'] = $reqData;  
	        	 $currData['user'] = $arUser; 
	        }else{
	        	 $currData = $arUser;
	        }
			return $currData;
		}
	}
}
?>