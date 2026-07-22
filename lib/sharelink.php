<?php

namespace Ss\Errorloger;

use Bitrix\Main\Config\Option;

final class ShareLink
{
  private const MODULE_ID = 'ss.errorloger';
  private const TTL = 259200;

  public static function create(): array
  {
    $token = bin2hex(random_bytes(32));
    $expiresAt = time() + self::TTL;
    Option::set(self::MODULE_ID, 'share_token_hash', hash('sha256', $token));
    Option::set(self::MODULE_ID, 'share_expires_at', (string)$expiresAt);
    return ['token' => $token, 'expires_at' => $expiresAt];
  }

  public static function validate(string $token): bool
  {
    if (!preg_match('~^[a-f0-9]{64}$~', $token)) return false;
    $status = self::status();
    return $status['active'] && hash_equals($status['hash'], hash('sha256', $token));
  }

  public static function status(): array
  {
    $hash = Option::get(self::MODULE_ID, 'share_token_hash', '');
    $expiresAt = (int)Option::get(self::MODULE_ID, 'share_expires_at', '0');
    return [
      'active' => $hash !== '' && $expiresAt > time(),
      'hash' => $hash,
      'expires_at' => $expiresAt,
    ];
  }

  public static function revoke(): void
  {
    Option::delete(self::MODULE_ID, ['name' => 'share_token_hash']);
    Option::delete(self::MODULE_ID, ['name' => 'share_expires_at']);
  }
}

