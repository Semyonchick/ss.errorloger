<?php

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ss\Errorloger\LogsPage;
use Ss\Errorloger\ShareLink;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
Loc::loadMessages(__FILE__);

if (!Loader::includeModule('ss.errorloger')) {
  require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
  echo '<div class="adm-info-message">' . htmlspecialcharsbx(Loc::getMessage('SS_ERRORLOGER_INSTALL_REQUIRED')) . '</div>';
  require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
  return;
}

global $APPLICATION, $USER;
if (!is_object($USER) || !$USER->IsAdmin()) {
  $APPLICATION->AuthForm(Loc::getMessage('SS_ERRORLOGER_ACCESS_DENIED'));
}

$request = Context::getCurrent()->getRequest();
if ($request->isPost() && check_bitrix_sessid()) {
  $action = (string)$request->getPost('share_action');
  if ($action === 'create') {
    $created = ShareLink::create();
    $scheme = $request->isHttps() ? 'https' : 'http';
    $host = preg_replace('~[^A-Za-z0-9.:-]~', '', (string)$request->getHttpHost());
    $_SESSION['SS_ERRORLOGER_SHARE_URL'] = $scheme . '://' . $host
      . '/ss-errorloger/share/' . $created['token'];
  } elseif ($action === 'revoke') {
    ShareLink::revoke();
  }
  LocalRedirect('ss_logs.php?lang=' . LANGUAGE_ID);
}

$createdUrl = (string)($_SESSION['SS_ERRORLOGER_SHARE_URL'] ?? '');
unset($_SESSION['SS_ERRORLOGER_SHARE_URL']);
$query = trim((string)$request->getQuery('q'));
$share = ShareLink::status();

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
$APPLICATION->SetTitle(Loc::getMessage('SS_ERRORLOGER_PAGE_TITLE'));
LogsPage::render(LogsPage::payload($query), [
  'public' => false,
  'share' => $share,
  'createdUrl' => $createdUrl,
]);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
