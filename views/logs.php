<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$escape = static function ($value): string {
  return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$formatDate = static function ($value) use ($escape): string {
  $timestamp = strtotime((string)$value);
  return $escape($timestamp ? date('d.m.Y H:i:s', $timestamp) : $value);
};
if ($public):
?><!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $escape(Loc::getMessage('SS_ERRORLOGER_TITLE')) ?></title></head><body><?php
endif;
?>
<style>
  .ssel { max-width: 1480px; margin: 0 auto; color: #263238; font: 14px/1.45 Arial, sans-serif; }
  .ssel * { box-sizing: border-box; }
  .ssel__top { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 16px; margin: 0 0 18px; }
  .ssel__source { color: #687781; word-break: break-all; }
  .ssel__search { display: flex; gap: 8px; align-items: flex-start; }
  .ssel__search input { min-width: 300px; padding: 9px 11px; border: 1px solid #c8d0d5; border-radius: 4px; }
  .ssel__button { padding: 9px 14px; border: 0; border-radius: 4px; background: #2f7dd1; color: #fff; cursor: pointer; }
  .ssel__button--muted { background: #6d7880; }
  .ssel__stats { display: grid; grid-template-columns: repeat(3, minmax(140px, 1fr)); gap: 12px; margin: 0 0 18px; }
  .ssel__stat { padding: 15px 17px; border: 1px solid #dce3e7; border-radius: 8px; background: #fff; }
  .ssel__stat strong { display: block; font-size: 25px; line-height: 1.1; }
  .ssel__stat span { color: #71808a; }
  .ssel__share, .ssel__setup, .ssel__empty { margin: 0 0 18px; padding: 18px; border-radius: 8px; background: #fff; border: 1px solid #dce3e7; }
  .ssel__share { border-left: 4px solid #2f7dd1; }
  .ssel__share-row { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
  .ssel__share-url { flex: 1 1 520px; padding: 9px 11px; border: 1px solid #9ac3ec; border-radius: 4px; }
  .ssel__setup { border-left: 4px solid #e29b28; }
  .ssel__setup pre { overflow: auto; padding: 14px; background: #18232b; color: #eaf2f5; border-radius: 6px; }
  .ssel__list { display: grid; gap: 12px; }
  .ssel__card { overflow: hidden; border: 1px solid #dce3e7; border-radius: 9px; background: #fff; }
  .ssel__card-head { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; padding: 16px 18px; }
  .ssel__title { margin: 0 0 7px; font-size: 16px; word-break: break-word; }
  .ssel__badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 9px; }
  .ssel__badge { display: inline-block; padding: 3px 8px; border-radius: 999px; background: #edf2f5; color: #53636d; font-size: 12px; }
  .ssel__badge--danger { background: #ffe7e2; color: #a42e22; }
  .ssel__count { min-width: 80px; text-align: center; }
  .ssel__count strong { display: block; color: #b73527; font-size: 28px; line-height: 1; }
  .ssel__meta { display: grid; grid-template-columns: repeat(3, minmax(150px, 1fr)); gap: 10px; color: #60717b; }
  .ssel__meta strong { display: block; color: #263238; }
  .ssel__location { padding: 10px 18px; border-top: 1px solid #edf0f2; background: #f8fafb; font-family: Consolas, monospace; word-break: break-all; }
  .ssel details { border-top: 1px solid #edf0f2; }
  .ssel summary { padding: 11px 18px; cursor: pointer; color: #2f6fae; }
  .ssel__trace { overflow: auto; margin: 0; padding: 15px 18px; background: #18232b; color: #eaf2f5; white-space: pre-wrap; word-break: break-word; }
  @media (max-width: 760px) {
    .ssel__stats, .ssel__meta { grid-template-columns: 1fr; }
    .ssel__card-head { grid-template-columns: 1fr; }
    .ssel__count { text-align: left; }
    .ssel__search, .ssel__search input { width: 100%; min-width: 0; }
  }
</style>
<main class="ssel">
  <div class="ssel__top">
    <div>
      <h2 style="margin:0 0 5px"><?= $escape(Loc::getMessage('SS_ERRORLOGER_HEADING')) ?></h2>
      <div class="ssel__source"><?= $escape(Loc::getMessage('SS_ERRORLOGER_SOURCE')) ?>: <?= $source['path'] !== '' ? $escape($source['path']) : $escape(Loc::getMessage('SS_ERRORLOGER_NOT_CONFIGURED')) ?></div>
      <div class="ssel__source"><?= $escape(Loc::getMessage('SS_ERRORLOGER_GROUPING')) ?></div>
    </div>
    <form class="ssel__search" method="get">
      <input type="search" name="q" value="<?= $escape($query) ?>" placeholder="<?= $escape(Loc::getMessage('SS_ERRORLOGER_SEARCH_PLACEHOLDER')) ?>">
      <button class="ssel__button" type="submit"><?= $escape(Loc::getMessage('SS_ERRORLOGER_SEARCH')) ?></button>
    </form>
  </div>

  <?php if (!$public): ?>
    <section class="ssel__share">
      <h3 style="margin:0 0 9px"><?= $escape(Loc::getMessage('SS_ERRORLOGER_SHARE_HEADING')) ?></h3>
      <?php if ($createdUrl !== ''): ?>
        <p><?= $escape(Loc::getMessage('SS_ERRORLOGER_SHARE_CREATED')) ?></p>
        <div class="ssel__share-row"><input class="ssel__share-url" readonly value="<?= $escape($createdUrl) ?>"></div>
      <?php elseif (!empty($share['active'])): ?>
        <p><?= $escape(Loc::getMessage('SS_ERRORLOGER_SHARE_ACTIVE_UNTIL')) ?> <strong><?= date('d.m.Y H:i:s', (int)$share['expires_at']) ?></strong>. <?= $escape(Loc::getMessage('SS_ERRORLOGER_SHARE_ACTIVE_ACTION')) ?></p>
      <?php else: ?>
        <p><?= $escape(Loc::getMessage('SS_ERRORLOGER_SHARE_DESCRIPTION')) ?></p>
      <?php endif; ?>
      <div class="ssel__share-row">
        <form method="post"><?= bitrix_sessid_post() ?><input type="hidden" name="share_action" value="create"><button class="ssel__button" type="submit"><?= $escape(Loc::getMessage(!empty($share['active']) ? 'SS_ERRORLOGER_SHARE_REISSUE' : 'SS_ERRORLOGER_SHARE_CREATE')) ?></button></form>
        <?php if (!empty($share['active'])): ?><form method="post"><?= bitrix_sessid_post() ?><input type="hidden" name="share_action" value="revoke"><button class="ssel__button ssel__button--muted" type="submit"><?= $escape(Loc::getMessage('SS_ERRORLOGER_SHARE_REVOKE')) ?></button></form><?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!$source['configured']): ?>
    <section class="ssel__setup">
      <h3 style="margin-top:0"><?= $escape(Loc::getMessage('SS_ERRORLOGER_SETUP_HEADING')) ?></h3>
      <p><?= $escape(Loc::getMessage('SS_ERRORLOGER_SETUP_BEFORE')) ?> <code>return</code> <?= $escape(Loc::getMessage('SS_ERRORLOGER_SETUP_FILE')) ?> <code>/bitrix/.settings_extra.php</code>. <?= $escape(Loc::getMessage('SS_ERRORLOGER_SETUP_MERGE')) ?> <code>exception_handling</code>.</p>
      <pre><?= $escape($configurationExample) ?></pre>
      <p><?= $escape(Loc::getMessage('SS_ERRORLOGER_SETUP_DIRECTORY')) ?> <code>/local/logs</code> <?= $escape(Loc::getMessage('SS_ERRORLOGER_SETUP_DIRECTORY_AFTER')) ?></p>
    </section>
  <?php else: ?>
    <div class="ssel__stats">
      <div class="ssel__stat"><strong><?= (int)$stats['events'] ?></strong><span><?= $escape(Loc::getMessage('SS_ERRORLOGER_STATS_EVENTS')) ?></span></div>
      <div class="ssel__stat"><strong><?= (int)$stats['groups'] ?></strong><span><?= $escape(Loc::getMessage('SS_ERRORLOGER_STATS_GROUPS')) ?></span></div>
      <div class="ssel__stat"><strong><?= (int)$stats['repeated'] ?></strong><span><?= $escape(Loc::getMessage('SS_ERRORLOGER_STATS_REPEATED')) ?></span></div>
    </div>
    <?php if (!$source['readable']): ?>
      <div class="ssel__empty"><?= $escape($source['message']) ?><br><code><?= $escape($source['path']) ?></code></div>
    <?php elseif (!$groups): ?>
      <div class="ssel__empty"><?= $escape(Loc::getMessage('SS_ERRORLOGER_EMPTY')) ?></div>
    <?php else: ?>
      <div class="ssel__list">
        <?php foreach ($groups as $group): $entry = $group['entry']; ?>
          <article class="ssel__card">
            <div class="ssel__card-head">
              <div>
                <div class="ssel__badges">
                  <span class="ssel__badge ssel__badge--danger"><?= $escape($entry['level']) ?></span>
                  <span class="ssel__badge"><?= $escape($entry['type']) ?></span>
                  <span class="ssel__badge">#<?= $escape($group['fingerprint']) ?></span>
                </div>
                <h3 class="ssel__title"><?= $escape($entry['message']) ?></h3>
                <div class="ssel__meta">
                  <span><?= $escape(Loc::getMessage('SS_ERRORLOGER_FIRST_SEEN')) ?><strong><?= $formatDate($group['first_at']) ?></strong></span>
                  <span><?= $escape(Loc::getMessage('SS_ERRORLOGER_LAST_SEEN')) ?><strong><?= $formatDate($group['last_at']) ?></strong></span>
                  <span><?= $escape(Loc::getMessage('SS_ERRORLOGER_HOSTS')) ?><strong><?= $escape(implode(', ', array_keys($group['hosts'])) ?: '—') ?></strong></span>
                </div>
              </div>
              <div class="ssel__count"><strong><?= (int)$group['count'] ?></strong><span><?= $escape(Loc::getMessage('SS_ERRORLOGER_REPEATS')) ?></span></div>
            </div>
            <?php if ($entry['file'] !== ''): ?><div class="ssel__location"><?= $escape($entry['file']) ?><?= $entry['line'] !== '' ? ':' . (int)$entry['line'] : '' ?></div><?php endif; ?>
            <?php if ($entry['trace'] !== ''): ?><details><summary><?= $escape(Loc::getMessage('SS_ERRORLOGER_TRACE')) ?></summary><pre class="ssel__trace"><?= $escape($entry['trace']) ?></pre></details><?php endif; ?>
            <?php if (count($group['occurrences']) > 1): ?><details><summary><?= $escape(Loc::getMessage('SS_ERRORLOGER_OCCURRENCES')) ?> (<?= count($group['occurrences']) ?>)</summary><pre class="ssel__trace"><?= $escape(implode("\n", $group['occurrences'])) ?></pre></details><?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</main>
<?php if ($public): ?></body></html><?php endif; ?>
