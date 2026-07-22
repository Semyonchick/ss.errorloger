<?php
$escape = static function ($value): string {
  return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$formatDate = static function ($value) use ($escape): string {
  $timestamp = strtotime((string)$value);
  return $escape($timestamp ? date('d.m.Y H:i:s', $timestamp) : $value);
};
if ($public):
?><!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Ошибки PHP</title></head><body><?php
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
      <h2 style="margin:0 0 5px">Журнал ошибок PHP</h2>
      <div class="ssel__source">Источник: <?= $source['path'] !== '' ? $escape($source['path']) : 'не настроен' ?></div>
      <div class="ssel__source">Группировка: тип + сообщение + файл + строка.</div>
    </div>
    <form class="ssel__search" method="get">
      <input type="search" name="q" value="<?= $escape($query) ?>" placeholder="Ошибка, файл, хост…">
      <button class="ssel__button" type="submit">Найти</button>
    </form>
  </div>

  <?php if (!$public): ?>
    <section class="ssel__share">
      <h3 style="margin:0 0 9px">Временный доступ без авторизации</h3>
      <?php if ($createdUrl !== ''): ?>
        <p>Ссылка создана на 72 часа. После обновления страницы секретная часть больше не показывается.</p>
        <div class="ssel__share-row"><input class="ssel__share-url" readonly value="<?= $escape($createdUrl) ?>"></div>
      <?php elseif (!empty($share['active'])): ?>
        <p>Ссылка активна до <strong><?= date('d.m.Y H:i:s', (int)$share['expires_at']) ?></strong>. Можно отозвать её или выпустить новую.</p>
      <?php else: ?>
        <p>Ссылка открывает только эту страницу журнала, действует 72 часа и закрыта от индексации.</p>
      <?php endif; ?>
      <div class="ssel__share-row">
        <form method="post"><?= bitrix_sessid_post() ?><input type="hidden" name="share_action" value="create"><button class="ssel__button" type="submit"><?= !empty($share['active']) ? 'Выпустить новую' : 'Создать ссылку на 72 часа' ?></button></form>
        <?php if (!empty($share['active'])): ?><form method="post"><?= bitrix_sessid_post() ?><input type="hidden" name="share_action" value="revoke"><button class="ssel__button ssel__button--muted" type="submit">Отозвать</button></form><?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if (!$source['configured']): ?>
    <section class="ssel__setup">
      <h3 style="margin-top:0">Bitrix не настроен на запись ошибок в файл</h3>
      <p>Добавьте секцию ниже в массив <code>return</code> файла <code>/bitrix/.settings_extra.php</code>. Если секция <code>exception_handling</code> уже существует, объедините настройки, не создавая второй ключ.</p>
      <pre><?= $escape($configurationExample) ?></pre>
      <p>Создайте каталог <code>/local/logs</code> с правом записи для PHP. Перезагрузите страницу после появления первой записи.</p>
    </section>
  <?php else: ?>
    <div class="ssel__stats">
      <div class="ssel__stat"><strong><?= (int)$stats['events'] ?></strong><span>событий прочитано</span></div>
      <div class="ssel__stat"><strong><?= (int)$stats['groups'] ?></strong><span>уникальных ошибок</span></div>
      <div class="ssel__stat"><strong><?= (int)$stats['repeated'] ?></strong><span>повторяющихся групп</span></div>
    </div>
    <?php if (!$source['readable']): ?>
      <div class="ssel__empty"><?= $escape($source['message']) ?><br><code><?= $escape($source['path']) ?></code></div>
    <?php elseif (!$groups): ?>
      <div class="ssel__empty">Подходящих записей не найдено.</div>
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
                  <span>Впервые<strong><?= $formatDate($group['first_at']) ?></strong></span>
                  <span>Последний раз<strong><?= $formatDate($group['last_at']) ?></strong></span>
                  <span>Хосты<strong><?= $escape(implode(', ', array_keys($group['hosts'])) ?: '—') ?></strong></span>
                </div>
              </div>
              <div class="ssel__count"><strong><?= (int)$group['count'] ?></strong><span>повторений</span></div>
            </div>
            <?php if ($entry['file'] !== ''): ?><div class="ssel__location"><?= $escape($entry['file']) ?><?= $entry['line'] !== '' ? ':' . (int)$entry['line'] : '' ?></div><?php endif; ?>
            <?php if ($entry['trace'] !== ''): ?><details><summary>Стек вызовов и детали</summary><pre class="ssel__trace"><?= $escape($entry['trace']) ?></pre></details><?php endif; ?>
            <?php if (count($group['occurrences']) > 1): ?><details><summary>Последние срабатывания (<?= count($group['occurrences']) ?>)</summary><pre class="ssel__trace"><?= $escape(implode("\n", $group['occurrences'])) ?></pre></details><?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</main>
<?php if ($public): ?></body></html><?php endif; ?>

