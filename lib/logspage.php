<?php

namespace Ss\Errorloger;

final class LogsPage
{
  public static function payload(string $query): array
  {
    $source = LogSource::discover();
    $entries = $source['readable'] ? LogReader::entries($source['path']) : [];
    $groups = ErrorGrouper::group($entries, $query);
    return [
      'source' => $source,
      'groups' => $groups,
      'stats' => ErrorGrouper::statistics($groups),
      'query' => $query,
      'configurationExample' => LogSource::configurationExample(),
    ];
  }

  public static function render(array $payload, array $view = []): void
  {
    extract($payload, EXTR_SKIP);
    $public = (bool)($view['public'] ?? false);
    $share = $view['share'] ?? [];
    $createdUrl = (string)($view['createdUrl'] ?? '');
    $downloadUrl = (string)($view['downloadUrl'] ?? '');
    require dirname(__DIR__) . '/views/logs.php';
  }
}
