<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("1С-Битрикс: Управление сайтом");

$arUser = CUser::getByID(5)->Fetch(); // Vasiliy
//echo '<pre>'.print_r($arUser, true).'</pre><br>'; // Debug 

//echo phpinfo();
?>

<a href="/bitrix/admin/">Перейти в Панель Управления</a>.

<style>
input, textarea {padding:5px;margin:10px;}
</style>
<div style="padding:10px 50px;">
<?

function ssh_exec($ip, $command) {
    $connection = ssh2_connect($ip, 22);
	if (ssh2_auth_password($connection, 'ftpuser', '200811')) {
		$stream = ssh2_exec($connection, $command);
		stream_set_blocking($stream, true);
		$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
		echo "<pre>Results :\n";
		echo stream_get_contents($stream_out)."</pre>";
		fclose($stream);
	} else {
	 die('Public Key Authentication Failed');
	}
}

$tmp_image = '/home/bitrix/ext_www/magicmirror.techmas.ru/upload/iblock/01355cd98bc51190883e275aa3edd797.jpg';
echo $queryCppRate = '../home/ftpuser/files/ex/face_capture_upd ' . $tmp_image; 
echo "<br /><br />\n";

//echo ssh_exec('104.131.124.131', $queryCppRate);   

/*if( $curl = curl_init() ) {  
    $postdata = array(
       'FileToLoad' => "@".$tmp_image."; filename=\"$tmp_image\";"
    );

    curl_setopt($curl, CURLOPT_URL, "http://104.131.124.131/api/face.php");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($curl,CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_COOKIE, "");
    $out = curl_exec($curl);
    curl_close($curl);   
    
    echo '<pre>'.print_r($out, true).'</pre><br>'; // Debug 
}*/
//echo 'response '.$autoRate = shell_exec($queryCppRate); 
?>
<h3>Тест обновления Картинок / /app/photo/set_photo/</h3>
<form name="" action="http://104.131.124.131/api/face.php" method="post" enctype="multipart/form-data">
    Checkword <input style="width:400px;" name="checkword" type="text" value="<?=$arUser['CHECKWORD']?>"><br />
    Код папки <input name="album_code" type="text" value="MAIN_5"><br />
    Фотка 1 <input name="photo[0]" type="file" value=""><br /> 
    Оценка 1 <input name="user_rate[0]" type="text" value=""><br /> 
    Описалово 1 <textarea name="decsription[0]" rows=5 cols=20></textarea> 
    <br /><br />  
    Фотка 2 <input name="photo[1]" type="file" value=""><br /> 
    Оценка 2 <input name="user_rate[1]" type="text" value=""><br />
    Описалово 2 <textarea name="decsription[1]" rows=5 cols=20></textarea> 
    <br /><br />    
    Фотка 3 <input name="photo[2]" type="file" value=""><br /> 
    Оценка 3  <input name="user_rate[2]" type="text" value=""><br /> 
    Описалово 3 <textarea name="decsription[2]" rows=5 cols=20></textarea> 
    <br /><br /> 
    <input type="submit" value="Изменить">
</form>
</div> 

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>