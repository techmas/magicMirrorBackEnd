<?php
/**
 * Created by PhpStorm.
 * User: Артемий
 * Date: 06.02.2017
 * Time: 0:47
 */

namespace Techmas\Pobeda;


class Tools
{


	public static function ReturnAnswerToAPP($DATA)
	{
		header('Content-Type: application/json');

		if (!isset($DATA["data"]))
			$DATA["data"] = "";

		if (!isset($DATA["data_type"]))
			$DATA["data_type"] = gettype($DATA["data"]);

		if (!isset($DATA["status"]) || !isset($DATA["status_msg"]) || !isset($DATA["status_code"]))
		{
			print json_encode(
				array("status" => "ERROR",
					"status_msg" => "Ошибка формата данных",
					"status_code" => "answer_format_error",
					"sessid" => bitrix_sessid(),
					"data_type" => "string",
					"data" => ""), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			);
			require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
			exit;
		}

		print json_encode(
			array("status" => $DATA["status"],
				"status_msg" => $DATA["status_msg"],
				"status_code" => $DATA["status_code"],
				"sessid" => bitrix_sessid(),
				"data_type" => $DATA["data_type"],
				"data" => $DATA["data"]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
		require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
		exit;
	}


	public static function ClearResult(&$arFields)
	{
		foreach ($arFields as $key => $value)
		{
			if (0 === strpos($key,'~'))
			{
				unset($arFields[$key]);
			}
			else
			{
				if (true == is_array($value))
					self::ClearResult($arFields[$key]);
			}
		}
	}


}
