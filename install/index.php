<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;

class ss_errorloger extends CModule
{
  public $MODULE_ID = 'ss.errorloger';
  public $MODULE_VERSION;
  public $MODULE_VERSION_DATE;
  public $MODULE_NAME = 'Анализ ошибок PHP';
  public $MODULE_DESCRIPTION = 'Группирует журнал ошибок Bitrix и создаёт временные ссылки только для чтения.';

  public function __construct()
  {
    $arModuleVersion = [];
    include __DIR__ . '/version.php';
    $this->MODULE_VERSION = $arModuleVersion['VERSION'];
    $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
  }

  public function DoInstall()
  {
    ModuleManager::registerModule($this->MODULE_ID);
    $this->InstallFiles();
    $events = EventManager::getInstance();
    $events->unRegisterEventHandler(
      'main',
      'OnBeforeProlog',
      $this->MODULE_ID,
      'Ss\\Errorloger\\ShareHandler',
      'handle'
    );
    $events->registerEventHandler(
      'main',
      'OnBeforeProlog',
      $this->MODULE_ID,
      'Ss\\Errorloger\\ShareHandler',
      'handle'
    );
  }

  public function DoUninstall()
  {
    EventManager::getInstance()->unRegisterEventHandler(
      'main',
      'OnBeforeProlog',
      $this->MODULE_ID,
      'Ss\\Errorloger\\ShareHandler',
      'handle'
    );
    Option::delete($this->MODULE_ID);
    $this->UnInstallFiles();
    ModuleManager::unRegisterModule($this->MODULE_ID);
  }

  public function InstallFiles()
  {
    CopyDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
    return true;
  }

  public function UnInstallFiles()
  {
    DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
    return true;
  }
}

