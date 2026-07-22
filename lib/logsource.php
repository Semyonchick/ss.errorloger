<?php

namespace Ss\Errorloger;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Localization\Loc;

final class LogSource
{
  public static function discover(): array
  {
    Loc::loadMessages(__FILE__);
    $handling = Configuration::getValue('exception_handling');
    $file = self::findFile(is_array($handling) ? $handling : []);
    if ($file === '') {
      return [
        'configured' => false,
        'path' => '',
        'readable' => false,
        'message' => Loc::getMessage('SS_ERRORLOGER_SOURCE_NOT_CONFIGURED'),
      ];
    }

    $path = self::resolvePath($file);
    return [
      'configured' => true,
      'path' => $path,
      'readable' => is_file($path) && is_readable($path),
      'message' => is_file($path) && is_readable($path)
        ? ''
        : Loc::getMessage('SS_ERRORLOGER_SOURCE_NOT_READABLE'),
    ];
  }

  public static function configurationExample(): string
  {
    return <<<'PHP'
'exception_handling' => [
  'value' => [
    'debug' => false,
    'handled_errors_types' => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED,
    'exception_errors_types' => E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT & ~E_DEPRECATED,
    'ignore_silence' => false,
    'assertion_throws_exception' => true,
    'assertion_error_type' => 256,
    'log' => [
      'settings' => [
        'file' => '#DOCUMENT_ROOT#/local/logs/php-errors.log',
        'log_size' => 10000000,
      ],
    ],
  ],
  'readonly' => false,
],
PHP;
  }

  private static function findFile(array $handling): string
  {
    $candidates = [
      $handling['log']['settings']['file'] ?? null,
      $handling['log']['file'] ?? null,
      $handling['settings']['file'] ?? null,
    ];
    foreach ($candidates as $candidate) {
      if (is_string($candidate) && trim($candidate) !== '') return trim($candidate);
    }
    return '';
  }

  private static function resolvePath(string $file): string
  {
    $root = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
    $path = str_replace(['#DOCUMENT_ROOT#', '{DOCUMENT_ROOT}'], $root, $file);
    if (preg_match('~^(?:[A-Za-z]:[\\\\/]|/)~', $path)) return $path;
    return $root . '/' . ltrim($path, '/\\');
  }
}
