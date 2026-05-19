<?php
declare(strict_types=1);

/** @var array $logs */
/** @var array $filters */

$formatLogo = function($path) {
    if (!$path) return null;
    $path = ltrim($path, '/');
    return (strpos($path, 'uploads/') === 0) ? '/' . $path : '/uploads/' . $path;
};
?>

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Sora', sans-serif; }
    body { background: #f5f5f5; margin: 0; }
    h1 { font-weight: 700; color: #0D2240; }
    .header { width: 95%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; transition: all 0.2s; }
    .btn:hover { background: #2d5bff; }

    .filters { margin: 0 auto 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); display: flex; flex-direction: row; align-items: flex-end; gap: 8px; overflow-x: auto; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 11px; font-weight: bold; color: #555; text-transform: uppercase; white-space: nowrap; }
    .filter-group input, .filter-group select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; outline: none; width: 130px; font-family: 'Sora', sans-serif; }

    .btn-filter { background: #1a2e6e; color: white; border: none; padding: 0 20px; border-radius: 4px; font-weight: bold; cursor: pointer; height: 35px; transition: 0.2s; font-family: 'Sora', sans-serif; }
    .btn-clear { background: #ddd; color: #333; padding: 0 15px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; height: 35px; display: flex; align-items: center; white-space: nowrap; }

    .container { width: 95%; margin: auto; }
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; margin-bottom: 50px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8f9ff; color: #555; padding: 12px; text-align: left; border: 1px solid #eee; font-size: 12px; text-transform: uppercase; }
    td { padding: 10px; border: 1px solid #eee; font-size: 13px; vertical-align: top; }
    tr:hover { background: #fafbff; }
    img { border-radius: 6px; object-fit: contain; border: 1px solid #f0f0f0; background: #fff; }
</style>

<div class="header">
    <h1>Histórico de Alterações</h1>
    <a href="/bus-companies" class="btn">Voltar</a>
</div>

<div class="container">
    <form method="GET" action="/bus-companies/logs" class="filters">
        <div class="filter-group">
            <label>ID Log</label>
            <input type="number" name="id" value="<?= htmlspecialchars((string)($filters['id'] ?? '')) ?>">
        </div>
        <div class="filter-group">
            <label>ID Usuário</label>
            <input type="number" name="user_id" value="<?= htmlspecialchars((string)($filters['user_id'] ?? '')) ?>">
        </div>
        <div class="filter-group">
            <label>ID Viação</label>
            <input type="number" name="bus_id" value="<?= htmlspecialchars((string)($filters['bus_id'] ?? '')) ?>">
        </div>
        <div class="filter-group">
            <label>Nome Viação</label>
            <input type="text" name="bus_name" placeholder="Ex: Penha" value="<?= htmlspecialchars((string)($filters['bus_name'] ?? '')) ?>">
        </div>
        <div class="filter-group">
            <label>Ação</label>
            <select name="action">
                <option value="">Todas</option>
                <option value="create" <?= ($filters['action'] ?? '') === 'create' ? 'selected' : '' ?>>CREATE</option>
                <option value="update" <?= ($filters['action'] ?? '') === 'update' ? 'selected' : '' ?>>UPDATE</option>
                <option value="delete" <?= ($filters['action'] ?? '') === 'delete' ? 'selected' : '' ?>>DELETE</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Data</label>
            <input type="date" name="date" value="<?= htmlspecialchars((string)($filters['date'] ?? '')) ?>">
        </div>

        <button type="submit" class="btn-filter">Filtrar</button>

        <?php if (!empty(array_filter($filters))): ?>
            <a href="/bus-companies/logs" class="btn-clear">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="card">
        <?php if (empty($logs)): ?>
            <div style="padding:20px;color: #555;">Nenhum log encontrado.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Usuário</th>
                    <th>ID Viação</th>
                    <th>Logo</th>
                    <th>Viação</th>
                    <th>Ação</th>
                    <th>Antes</th>
                    <th>Depois</th>
                    <th>Data</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <?php
                    $old = !empty($log['old_value']) ? json_decode((string)$log['old_value'], true) : null;
                    $new = !empty($log['new_value']) ? json_decode((string)$log['new_value'], true) : null;
                    $changes = [];
                    if ($log['action'] === 'update' && is_array($old) && is_array($new)) {
                        foreach ($new as $key => $value) {
                            if (in_array($key, ['updated_at', 'created_at'])) continue;
                            $oldValue = $old[$key] ?? null;
                            if ($oldValue != $value) $changes[$key] = ['old' => $oldValue, 'new' => $value];
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $log['id'] ?></td>
                        <td><?= $log['user_id'] ?? '0' ?></td>
                        <td><?= $log['bus_company_id'] ?? ($new['id'] ?? ($old['id'] ?? '-')) ?></td>
                        <td>
                            <?php
                            $logoField = ($log['action'] === 'delete') ? ($old['logo'] ?? null) : ($new['logo'] ?? ($old['logo'] ?? null));
                            $logoUrl = $formatLogo($logoField);
                            ?>
                            <?php if ($logoUrl): ?>
                                <img src="<?= $logoUrl ?>" width="45" height="45">
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <?php $nome = $log['name'] ?? ($new['name'] ?? ($old['name'] ?? 'Viação Removida')); ?>
                            <strong><?= htmlspecialchars((string)$nome) ?></strong>
                        </td>
                        <td style="font-weight: bold; color: <?= $log['action'] === 'delete' ? '#dc3545' : ($log['action'] === 'create' ? '#28a745' : '#ffc107') ?>;">
                            <?= strtoupper($log['action']) ?>
                        </td>
                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <?php foreach ($changes as $k => $v): ?> <div><small><strong><?= $k ?>:</strong> <?= htmlspecialchars((string)($v['old'] ?? '')) ?></small></div> <?php endforeach; ?>
                            <?php elseif ($log['action'] === 'delete'): ?> <small style="color:gray">Registro removido</small>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <?php foreach ($changes as $k => $v): ?> <div><small><strong><?= $k ?>:</strong> <?= htmlspecialchars((string)($v['new'] ?? '')) ?></small></div> <?php endforeach; ?>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td style="white-space: nowrap;"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>