<?php
/**
 * Created by PhpStorm.
 * User: Артемий
 * Date: 08.12.2015
 * Time: 22:33
 */
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

Class techmas_pobeda extends CModule
{
	var $MODULE_ID = 'techmas.pobeda';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	
	
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__ . "/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("techmas.pobeda_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("techmas.pobeda_MODULE_DESC");
		$this->PARTNER_NAME = Loc::getMessage("techmas.pobeda_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("techmas.pobeda_PARTNER_URI");
	}
	
	
	function InstallFiles()
	{
		//CopyDirFiles(Main::GetPatch()."/install/components/reznikov", $_SERVER["DOCUMENT_ROOT"]."/local/components/academy", true, true);
		return true;
	}
	
	function InstallDB()
	{
/*
		if (!Application::getConnection(\Techmas\Mgts\user\TrusteeTable::getConnectionName())
			->isTableExists(
				Base::getInstance('\Techmas\Mgts\user\TrusteeTable')->getDBTableName()
			)
		)
		{
			Base::getInstance('\Techmas\Mgts\user\TrusteeTable')->createDbTable();
		}*/


		RegisterModule("techmas.pobeda");
	}
	
	function UnInstallDB()
	{
		//\Bitrix\Main\Application::getConnection()->queryExecute("DROP TABLE IF EXISTS TABLE_NAME");

		//\Bitrix\Main\Config\Option::delete($this->MODULE_ID);
	}
	
	function DoInstall()
	{
		
		//$APPLICATION->IncludeAdminFile(GetMessage("VOTE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vote/install/step.php");
		if ($this->InstallDB())
		{
			//$this->InstallEvents();
			//$this->InstallFiles();
		}
		
	}
	
	/**
	 *
	 */
	function DoUninstall()
	{
		$this->UnInstallDB();
		//$this->UnInstallEvents();
		//$this->UnInstallFiles()
		UnRegisterModule("techmas.pobeda");
	}
	
	
/*
	function GetModuleRightList()    // Создаем права для нашего модуля
	{
		return array(
			"reference_id" => array("D", "F", "G", "L", "M", "MM", "O", "U", "W"),
			"reference" => array(
				"[D] Доступ закрыт",
				"[F] Мастер",
				"[G] Оператор партнёра",
				"[L] Оператор ITR",
				"[M] Куратор",
				"[MM] Куратор2",
				"[O] Контролёр",
				"[U] Оператор МГТС",
				"[W] Полный администратор"
			));
	}
	*/
	
	
}