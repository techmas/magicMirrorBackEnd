<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/pobeda.log");
use \Bitrix\Sale\Order;
use \Bitrix\Sale\Internals\ShipmentTable;


// Обнуление отгрузок
function rewriteShipment($ORDER_ID){  
	
	// Редактируем отгрузки
    $filterSH['=SYSTEM'] = 'N';
	$filterSH['ORDER_ID'] = $ORDER_ID;

	$paramSH = array(
		'filter' => $filterSH,
	);

	$shipmentArr =  ShipmentTable::getList($paramSH) -> fetch(); 

	if(!empty($shipmentArr['ID'])){
		$saleOrder = Order::load($ORDER_ID);
		$shipment = $saleOrder->getShipmentCollection()->getItemById($shipmentArr['ID']);
		if($shipment){
							
			$updateResult = $shipment->setField('DELIVERY_ID', 2);
			$updateResult = $shipment->setField('CUSTOM_PRICE_DELIVERY', "Y");
			$updateResult = $shipment->setField('BASE_PRICE_DELIVERY', '0.00');

			$result = $saleOrder->save(); 
	    }
		    
	    return true; 
		    
    }else{      	
    	return false;
    }	
}

// Безопасная загрузка исполняемых файлов
function syntax_check_php_file ($file) {   
    // получим содержимое проверяемого файла
    @$code = file_get_contents($file);
     
    // файл не найден
    if ($code === false) {
        throw new Exception('File '.$file.' does not exist');
    }
     
    // первый этап проверки
    $braces = 0;
    $inString = 0;
    foreach ( token_get_all($code) as $token ) {
        if ( is_array($token) ) {
            switch ($token[0]) {
                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                case T_START_HEREDOC: ++$inString; break;
                case T_END_HEREDOC:   --$inString; break;
            }
        }
        else if ($inString & 1) {
            switch ($token) {
                case '`':
                case '"': --$inString; break;
            }
        }
        else {
            switch ($token) {
                case '`':
                case '"': ++$inString; break;
 
                case '{': ++$braces; break;
                case '}':
                    if ($inString) {
                        --$inString;
                    }
                    else {
                        --$braces;
                        if ($braces < 0) {
                            throw new Exception('Braces problem!');
                        }
                    }
                break;
            }
        }
    }
     
    // расхождение в открывающих-закрывающих фигурных скобках
    if ($braces) {
        throw new Exception('Braces problem!');
    }
     
    $res = false;
     
    // второй этап проверки
    ob_start();
    $res = eval('if (0) {?>'.$code.'<?php }; return true;');
    $error_text = ob_get_clean();
     
    // устранение ошибки 500 в функции eval(), при директиве display_errors = off;
    header('HTTP/1.0 200 OK');
     
    if (!$res) {
        throw new Exception($error_text);
    }
     
    return true;
}

// Безопасный require. $viewError = true - показывать ошибки
function _require($fileExec, $viewError = false){
	
	try {
		if ( syntax_check_php_file($fileExec) ) {
		    require $fileExec;
		}
	}
	catch (Exception $e) {  
		if(!empty($viewError)){
	    	echo $e;
	    } 
	}
}

// Безопасный require_once. $viewError = true - показывать ошибки
function _require_once($fileExec, $viewError = false){
	
	try {
		if ( syntax_check_php_file($fileExec) ) {
		    require_once $fileExec;
		}
	}
	catch (Exception $e) {  
		if(!empty($viewError)){
	    	echo $e;
	    } 
	}
}

// Безопасный include.
function _include($fileExec, $viewError = false){
	
	try {
		if ( syntax_check_php_file($fileExec) ) {
		   include $fileExec;
		}
	}
	catch (Exception $e) {  
		if(!empty($viewError)){
	    	echo $e;
	    } 
	}
}

// Безопасный include_once.
function _include_once($fileExec, $viewError = false){
	
	try {
		if ( syntax_check_php_file($fileExec) ) {
		   include_once $fileExec;
		}
	}
	catch (Exception $e) {  
		if(!empty($viewError)){
	    	echo $e;
	    } 
	}
}

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule('pull');

// Основной класс  RESTful API
_require_once($_SERVER['DOCUMENT_ROOT'].'/app/user/init.php', true);
$RM = new RestMain();

// Класс для работы с Водилами
/*_require_once($_SERVER['DOCUMENT_ROOT'].'/app/driver/init.php', true);
$DR = new Driver();

// Класс для работы с Google API и заказами
_require_once($_SERVER['DOCUMENT_ROOT'].'/app/order/init.php', true);
$GMaps = new GoogleMaps();
$UOrder = new UserOrders();*/

//RegisterModuleDependences("main", "OnProlog", "main", "", "", 2, $_SERVER['DOCUMENT_ROOT']."/lib/pull_hit.php");

//define('PULL_USER_ID', 400000000);
?>