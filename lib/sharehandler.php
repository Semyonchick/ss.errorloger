<?php

namespace Ss\Errorloger;

use Bitrix\Main\Context;
use Bitrix\Main\Loader;

final class ShareHandler
{
  public static function handle(): void
  {
    $request = Context::getCurrent()->getRequest();
    $path = parse_url($request->getRequestUri(), PHP_URL_PATH);
    if (!preg_match('~^/ss-errorloger/share/([a-f0-9]{64})/?$~', (string)$path, $match)) return;

    global $APPLICATION;
    $APPLICATION->RestartBuffer();
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    header('Referrer-Policy: no-referrer');
    header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; form-action 'self'; base-uri 'none'");
    if (!in_array($request->getRequestMethod(), ['GET', 'HEAD'], true) || !ShareLink::validate($match[1])) {
      http_response_code(404);
      echo '<!doctype html><meta charset="utf-8"><title>Ссылка недоступна</title>';
      echo '<p>Ссылка истекла, была отозвана или указана неверно.</p>';
      die();
    }

    Loader::includeModule('ss.errorloger');
    $query = trim((string)$request->getQuery('q'));
    LogsPage::render(LogsPage::payload($query), ['public' => true]);
    die();
  }
}

