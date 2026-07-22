<?php

namespace Ss\Errorloger;

final class ErrorGrouper
{
  public static function group(array $entries, string $query = '', int $limit = 250): array
  {
    $groups = [];
    foreach ($entries as $entry) {
      if ($query !== '' && mb_stripos(implode(' ', $entry), $query) === false) continue;
      $key = hash('sha256', implode("\n", [
        mb_strtolower(preg_replace('~\s+~u', ' ', $entry['level'])),
        mb_strtolower(preg_replace('~\s+~u', ' ', $entry['type'])),
        preg_replace('~\s+~u', ' ', trim($entry['message'])),
        str_replace('\\', '/', mb_strtolower($entry['file'])),
        (string)$entry['line'],
      ]));
      if (!isset($groups[$key])) {
        $groups[$key] = [
          'fingerprint' => substr($key, 0, 12),
          'count' => 0,
          'first_at' => $entry['date'],
          'first_ts' => $entry['timestamp'],
          'last_at' => $entry['date'],
          'last_ts' => $entry['timestamp'],
          'hosts' => [],
          'entry' => $entry,
          'occurrences' => [],
        ];
      }
      $group = &$groups[$key];
      $group['count']++;
      if ($entry['timestamp'] < $group['first_ts'] || $group['first_ts'] === 0) {
        $group['first_at'] = $entry['date'];
        $group['first_ts'] = $entry['timestamp'];
      }
      if ($entry['timestamp'] >= $group['last_ts']) {
        $group['last_at'] = $entry['date'];
        $group['last_ts'] = $entry['timestamp'];
        $group['entry'] = $entry;
      }
      if ($entry['host'] !== '') $group['hosts'][$entry['host']] = true;
      $group['occurrences'][] = $entry['date'];
      if (count($group['occurrences']) > 10) array_shift($group['occurrences']);
      unset($group);
    }

    usort($groups, static function (array $left, array $right): int {
      return $right['last_ts'] <=> $left['last_ts'];
    });
    return array_slice($groups, 0, $limit);
  }

  public static function statistics(array $groups): array
  {
    $events = 0;
    $repeated = 0;
    foreach ($groups as $group) {
      $events += $group['count'];
      if ($group['count'] > 1) $repeated++;
    }
    return ['events' => $events, 'groups' => count($groups), 'repeated' => $repeated];
  }
}

