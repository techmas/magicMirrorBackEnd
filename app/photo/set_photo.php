<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;

$checkword = $_REQUEST['checkword'];
$name = htmlspecialchars(strip_tags($_REQUEST['name']));
$albumCode = htmlspecialchars(strip_tags($_REQUEST['album_code']));
$description = htmlspecialchars(strip_tags($_REQUEST['description'])); 

//$arUser = CUser::getByID(130)->Fetch(); 
//echo '<pre>'.print_r($arUser, true).'</pre><br>'; // Debug 

$filter = array("CHECKWORD" => $checkword, "ACTIVE" => "Y");

if(!empty($checkword) && $arUser = CUser::GetList(($by="id"), ($order="desc"), $filter)->Fetch()){    
     
    // Проверяем на массовость. Если одна - засовываем в массив
    if(!is_array($_FILES['photo']['error'])){        foreach($_FILES['photo'] as $field => $value){        	$photoArr[$field][0] = $value;
        }	
    }else{        $photoArr = $_FILES['photo'];	
    }
  
    
    // Получаем ID альбома
    $getAlbum = CIBlockSection::GetList(
        Array(), 
        array(
            'IBLOCK_CODE' => 'albums',
            'CODE' => ((!empty($albumCode)) ? $albumCode : "MAIN_".$arUser['ID']),
        )
    ) -> fetch();
    
    foreach($photoArr['error'] as $key => $photoError){
    
        if(empty($photoError)){    
        
            $currPhotoData['name'] = $photoArr['name'][$key];  
            $currPhotoData['type'] = $photoArr['type'][$key];
            $currPhotoData['tmp_name'] = $photoArr['tmp_name'][$key];
            $currPhotoData['error'] = $photoArr['error'][$key];
            $currPhotoData['size'] = $photoArr['size'][$key]; 
            $currPhotoData['del'] = 'N';
            $currPhotoData['MODULE_ID'] = 'iblock';  
            $currPhotoData['description'] = $description;  
        
	        //echo '<pre>'.print_r($currPhotoData, true).'</pre><br>'; // Debug    
	        //$arFileAvatar = CFile::MakeFileArray(CFile::SaveFile($currPhotoData));  
	        
	        $el = new CIBlockElement;
	        
	        $fid = CFile::SaveFile($currPhotoData);   
	        
	        $image = CFile::GetFileArray($fid);
   
            $image_src = $_SERVER['DOCUMENT_ROOT'] . $image['SRC'];
            $tmp_image = $_SERVER['DOCUMENT_ROOT'] . '/upload/iblock/' . $image['FILE_NAME'];
   
			CFile::ResizeImageFile(
			    $image_src, 
			    $tmp_image, 
			    array('width' => 163, 'height' => 134), 
			    BX_RESIZE_IMAGE_PROPORTIONAL
			); 
			
			$queryCppRate = 'root@genom:~# cd ../home/ftpuser/files/ex/face_capture ' . $tmp_image;  
			$autoRate = shell_exec($queryCppRate); 
			
			//echo 'Зпрос оценки '.$queryCppRate;   
			
			$property = array(
				"USER_RATE"   => intval($_REQUEST['user_rate'][$key]),
				"SYSTEM_RATE" => $autoRate,
			);
	        
	        $arPhotoAdd= Array(  
	            "MODIFIED_BY"       => $arUser['ID'], 
	            "IBLOCK_SECTION_ID" => $getAlbum['ID'],
	            "IBLOCK_ID"         => 1,  
	            "PROPERTY_VALUES"   => $property,  
	            "NAME"              => "Картинка ".$currPhotoData['name'],  
	            "ACTIVE"            => "Y",  
	            "PREVIEW_PICTURE"   => CFile::MakeFileArray($tmp_image),
	            "DETAIL_PICTURE"    => CFile::MakeFileArray($fid),
	            "DETAIL_TEXT"       => $description
	        );
	        
	        if($PHOTO_ID = $el->Add($arPhotoAdd)){	        	$data['succes'][$key] = "#".$key." загружено";
	        }else{	            $data['error'][$key] = "Ошибка загрузки: ".$el->LAST_ERROR;		
	        }	
   
        }else{        	             $data['error'][$key] = "Ошибка загрузки #".$photoError;	
        }
    } 
    
    //echo '<pre>'.print_r($data, true).'</pre><br>'; // Debug 
   	returnJSON(
        "succes", 
        1, 
        "Результат загрузки",
        $data
    );
    

}else{
    returnJSON(
        "error", 
        0, 
        "Пользователь не найден"
    );
}