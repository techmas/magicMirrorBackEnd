<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;

$checkword = $_REQUEST['checkword'];
$name = htmlspecialchars(strip_tags($_REQUEST['name']));
$albumCode = htmlspecialchars(strip_tags($_REQUEST['album_code']));

//$arUser = CUser::getByID(130)->Fetch(); 
//echo '<pre>'.print_r($arUser, true).'</pre><br>'; // Debug 

$filter = array("CHECKWORD" => $checkword, "ACTIVE" => "Y");

if(!empty($checkword) && $arUser = CUser::GetList(($by="id"), ($order="desc"), $filter)->Fetch()){ 
       
    if(!empty($albumCode)){
    
        $ownerAlbumArr = array_reverse(explode('_', $albumCode));         
        $ownerAlbum = $ownerAlbumArr[0];

        if($ownerAlbum == $arUser['ID'] || $albumCode == 'new'){  
              
            // Запрещаем менять код главного альбома
            if($albumCode == "MAIN_".$arUser){
        	    $newAlbumCode = $albumCode;
            }else{            	
                $newAlbumCode = replace_alias($name).'_'.$arUser['ID']; 
            }
            
            $rootFolder = CIBlockSection::GetList(
                Array(), 
                array(
                    'IBLOCK_CODE' => 'albums',
                    'CODE' => 'USER_'.$arUser['ID'],
                )
            ) -> fetch();
        
            $getAlbum = CIBlockSection::GetList(
                Array(), 
                array(
                    'IBLOCK_CODE' => 'albums',
                    'CODE' => $albumCode,
                )
            ) -> fetch();     
            
            //echo '<pre>'.print_r($rootFolder, true).'</pre><br>'; // Debug  
    
		    $setAlbum = new CIBlockSection;
		    $arFields = Array(  
		        "ACTIVE" => 'Y',  
		        "CODE" => $newAlbumCode,   
		        "IBLOCK_ID" => 1,  
		        "NAME" => $name    
		    );	
		    
		    if(!empty($getAlbum)){		    	if($is_update = $setAlbum -> Update($getAlbum['ID'], $arFields)){
		    	    $albumID = $getAlbum['ID']; 
		    	}
		    }elseif($albumCode == 'new'){  
		        $arFields['IBLOCK_SECTION_ID'] = $rootFolder['ID'];     
		        $albumID = $setAlbum -> Add($arFields);   
		    } 
		    
		    if(!empty($albumID)){
			    returnJSON(
			        "success", 
			        1, 
			        "Альбом успешно создан/обновлен", 
			        array('ALBUM_CODE' => $newAlbumCode, 'ALBUM_ID' => $albumID)
			    );
		    }else{		    	returnJSON(
			        "error", 
			        0, 
			        "Не удалось создать/обновить альбом" 
			    );
		    }        	
        }else{        	returnJSON(
		        "error", 
		        0, 
		        "Альбом не принадлежит пользователю" 
		    );
        }
        
    }else{    	returnJSON(
	        "error", 
	        0, 
	        "Не указан код альбома"
	    );
    } 
    
}else{
   returnJSON(
        "error", 
        0, 
        "Пользователь не найден"
    );
}