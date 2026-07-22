<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('ss.errorloger', [
  'Ss\\Errorloger\\ErrorGrouper' => 'lib/errorgrouper.php',
  'Ss\\Errorloger\\LogReader' => 'lib/logreader.php',
  'Ss\\Errorloger\\LogSource' => 'lib/logsource.php',
  'Ss\\Errorloger\\LogsPage' => 'lib/logspage.php',
  'Ss\\Errorloger\\ShareHandler' => 'lib/sharehandler.php',
  'Ss\\Errorloger\\ShareLink' => 'lib/sharelink.php',
]);

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

  $moduleMenu[$menuIndex]['parent_menu'] = 'global_menu_services';
}, 900);
