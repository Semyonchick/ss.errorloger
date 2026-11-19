<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = [
  'parent_menu' => 'global_menu_services',
  'sort' => 500,
  'text' => Loc::getMessage('SS_ERRORLOGER_MENU_TEXT'),
  'title' => Loc::getMessage('SS_ERRORLOGER_MENU_TITLE'),
  'url' => 'ss_logs.php?lang=' . LANGUAGE_ID,
  'more_url' => ['ss_logs.php'],
  'items_id' => 'menu_smartsam_errorlogger',
];

AddEventHandler('main', 'OnBuildGlobalMenu', static function (&$globalMenu, &$moduleMenu) {
  $menuIndex = null;
  foreach ($moduleMenu as $index => $menuItem) {
    if (($menuItem['items_id'] ?? null) !== 'menu_smartsam_errorlogger') continue;

    $menuIndex = $index;
    break;
  }
  if ($menuIndex === null) return;

  foreach ($globalMenu as &$globalItem) {
    if (($globalItem['items_id'] ?? null) !== 'menu_ss') continue;

    $errorItem = $moduleMenu[$menuIndex];
    unset($errorItem['parent_menu'], $errorItem['section']);
    unset($moduleMenu[$menuIndex]);
    $moduleMenu = array_values($moduleMenu);

    $items = array_values(array_filter($globalItem['items'] ?? [], static function ($item) {
      return ($item['items_id'] ?? null) !== 'menu_smartsam_errorlogger';
    }));
    $monitoringPosition = null;
    foreach ($items as $position => $item) {
      if (($item['items_id'] ?? null) !== 'menu_smartsam_monitoring') continue;

      $monitoringPosition = $position;
      break;
    }
    if ($monitoringPosition === null) {
      $items[] = $errorItem;
    } else {
      array_splice($items, $monitoringPosition, 0, [$errorItem]);
    }
    $globalItem['items'] = $items;
    return;
  }
}, 900);

return $aMenu;
