<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Neti\RestApi\HighLoadBlock\User;

Class neti_restapi extends CModule
{
    public $MODULE_ID;
    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_GROUP_RIGHTS = 'Y';

    function __construct()
    {
        include __DIR__ . '/version.php';
        $this->MESS_PREFIX = mb_strtoupper(get_class($this));
        $this->MODULE_ID = str_replace('_', '.', get_class($this));
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage('MODULE_NETI_RESTAPI_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_NETI_RESTAPI_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('PARTNER_NAME');
        $this->PARTNER_URI = 'https://netiweb.ru';

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $step = intval($step);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile('', $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/step1.php");
        }
        if ($step == 2) {
            RegisterModule($this->MODULE_ID);
            \CModule::IncludeModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallEvents();
            $APPLICATION->IncludeAdminFile(Loc::getMessage('MODULE_NETI_COMPLETE_INSTALL'), $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/step2.php");
        }
        return true;
    }

    function DoUninstall()
    {
        global $APPLICATION, $DOCUMENT_ROOT, $step;
        $step = intval($step);

        if ($step < 2) {
            $APPLICATION->IncludeAdminFile('', $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/unstep1.php");
        }
        if ($step == 2) {
            \CModule::IncludeModule($this->MODULE_ID);
            $context = \Bitrix\Main\Application::getInstance()->getContext();
            $request = $context->getRequest();
            $this->UnInstallEvents();
            UnRegisterModule($this->MODULE_ID);
            if (empty($request->getValues()['savedata'])) {
                $this->UnInstallDB();
            }
            $this->UnInstallFiles();
            $APPLICATION->IncludeAdminFile(Loc::getMessage('MODULE_NETI_COMPLETE_UNINSTALL'), $DOCUMENT_ROOT."/local/modules/" . $this->MODULE_ID . "/install/unstep2.php");
        }


        return true;
    }

    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible(
            'main',
            'OnAfterSetOption_' . \Neti\RestApi\Description::PARAMETER_NAME_PREFIX_INTERFACE,
            $this->MODULE_ID,
            '\\Neti\\RestApi\\Events\\Handlers\\Handler',
            'ChangePathInterface'
        );
        return true;
    }

    function InstallDB()
    {
        $hl = new User();
        $bool = $hl->create();
        return true;
    }

    function UnInstallDB()
    {
        $hl = new User();
        $bool = $hl->deleteHL();
        return true;
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnAfterSetOption_' . \Neti\RestApi\Description::PARAMETER_NAME_PREFIX_INTERFACE,
            $this->MODULE_ID,
            '\\Neti\\RestApi\\Events\\Handlers\\Handler',
            'ChangePathInterface'
        );
    }

    function InstallFiles()
    {
//        CopyDirFiles(
//            $_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/components/",
//            $_SERVER["DOCUMENT_ROOT"]."/local/components",
//            true, true
//        );
        return true;
    }

    function UnInstallFiles()
    {
//        DeleteDirFiles(
//            $_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/components/",
//            $_SERVER["DOCUMENT_ROOT"]."/local/components");
//        return true;
    }
}
?>