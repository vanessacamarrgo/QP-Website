<?php
/** @var array $pagination   ['page', 'pages', 'total', 'perPage'] */
/** @var string $baseUrl     URL base para montar os links, ex: '/bus-companies' */
/** @var array $queryParams  Filtros ativos para manter na paginação */

if (!isset($pagination) || $pagination['pages'] <= 1) return;

$current = $pagination['page'];
$total   = $pagination['pages'];

function buildPageUrl(string $base, array $params, int $page): string {
    $params['page'] = $page;
    return $base . '?' . http_build_query($params);
}
?>

<div class="pagination">
    <?php if ($current > 1): ?>
        <a href="<?= buildPageUrl($baseUrl, $queryParams, $current - 1) ?>" class="page-btn">‹ Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total; $i++): ?>
        <?php if ($i === $current): ?>
            <span class="page-btn page-btn--active"><?= $i ?></span>
        <?php elseif ($i === 1 || $i === $total || abs($i - $current) <= 2): ?>
            <a href="<?= buildPageUrl($baseUrl, $queryParams, $i) ?>" class="page-btn"><?= $i ?></a>
        <?php elseif (abs($i - $current) === 3): ?>
            <span class="page-ellipsis">…</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($current < $total): ?>
        <a href="<?= buildPageUrl($baseUrl, $queryParams, $current + 1) ?>" class="page-btn">Próximo ›</a>
    <?php endif; ?>

    <span class="page-info"><?= $pagination['total'] ?> registro(s)</span>
</div>

<style>
    .pagination { display: flex; align-items: center; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
    .page-btn { padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #1a2e6e; font-size: 13px; font-weight: 600; transition: 0.2s; }
    .page-btn:hover { background: #1a2e6e; color: white; border-color: #1a2e6e; }
    .page-btn--active { background: #1a2e6e; color: white; border-color: #1a2e6e; pointer-events: none; }
    .page-ellipsis { padding: 6px 4px; color: #999; font-size: 13px; }
    .page-info { margin-left: auto; font-size: 12px; color: #888; }
</style>