<?php

namespace Ss\Errorloger;

final class LogReader
{
  private const MAX_BYTES = 5_000_000;

  public static function entries(string $path): array
  {
    if ($path === '' || !is_file($path) || !is_readable($path)) return [];
    $content = self::tail($path);
    if ($content === '') return [];

    $blocks = preg_split('~\R-{10,}\R~', $content) ?: [];
    if (count($blocks) === 1) {
      $blocks = preg_split('~(?=\[(?:\d{2}-[A-Za-z]{3}-\d{4}|\d{4}-\d{2}-\d{2})[^\]]*\])~', $content) ?: [];
    }

    $entries = [];
    foreach ($blocks as $block) {
      $entry = self::parseBlock(trim($block));
      if ($entry !== null) $entries[] = $entry;
    }
    return $entries;
  }

  private static function tail(string $path): string
  {
    $size = (int)filesize($path);
    $offset = max(0, $size - self::MAX_BYTES);
    $handle = fopen($path, 'rb');
    if ($handle === false) return '';
    if ($offset > 0) {
      fseek($handle, $offset);
      fgets($handle);
    }
    $content = (string)stream_get_contents($handle);
    fclose($handle);
    return $content;
  }

  private static function parseBlock(string $block): ?array
  {
    if ($block === '') return null;
    if (preg_match(
      '~^(?<date>\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+-\s+Host:\s+(?<host>.*?)\s+-\s+(?<level>.*?)\s+-\s+\[(?<type>[^\]]+)]\s*\R(?<body>[\s\S]+)$~',
      $block,
      $match
    )) {
      return self::build($match['date'], $match['host'], $match['level'], $match['type'], $match['body']);
    }

    if (preg_match(
      '~^\[(?<date>[^]]+)]\s+(?:PHP\s+)?(?<type>Fatal error|Parse error|Warning|Notice|Deprecated|Error):\s*(?<body>[\s\S]+)$~i',
      $block,
      $match
    )) {
      return self::build($match['date'], '', strtoupper($match['type']), $match['type'], $match['body']);
    }

    return null;
  }

  private static function build(string $date, string $host, string $level, string $type, string $body): array
  {
    $body = trim($body);
    $lines = preg_split('/\R/', $body) ?: [];
    $message = trim((string)array_shift($lines));
    $file = '';
    $lineNumber = '';
    foreach ($lines as $index => $line) {
      if (preg_match('~^(.+?)(?::| on line )(\d+)$~', trim($line), $location)) {
        $file = trim($location[1]);
        $lineNumber = $location[2];
        unset($lines[$index]);
        break;
      }
    }
    if ($file === '' && preg_match('~\s+in\s+(.+?)\s+on line\s+(\d+)\s*$~i', $message, $location)) {
      $file = trim($location[1]);
      $lineNumber = $location[2];
      $message = trim(substr($message, 0, (int)strripos($message, ' in ')));
    }

    $timestamp = strtotime($date) ?: 0;
    return [
      'date' => $date,
      'timestamp' => $timestamp,
      'host' => trim($host),
      'level' => trim($level),
      'type' => trim($type),
      'message' => $message,
      'file' => $file,
      'line' => $lineNumber,
      'trace' => trim(implode("\n", $lines)),
    ];
  }
}

