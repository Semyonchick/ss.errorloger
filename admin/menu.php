<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = [
  'parent_menu' => 'menu_ss',
  'sort' => 500,
  'text' => Loc::getMessage('SS_ERRORLOGER_MENU_TEXT'),
  'title' => Loc::getMessage('SS_ERRORLOGER_MENU_TITLE'),
  'url' => 'ss_logs.php?lang=' . LANGUAGE_ID,
  'more_url' => ['ss_logs.php'],
  'items_id' => 'menu_ss_errorloger',
];
