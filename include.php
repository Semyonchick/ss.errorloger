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

