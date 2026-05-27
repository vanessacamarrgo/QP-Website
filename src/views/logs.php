<?php
declare(strict_types=1);

/** @var array $logs */
/** @var array $filters */
?>

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Sora', sans-serif; }
    body { background: #f5f5f5; margin: 0; }
    h1 { font-weight: 700; color: #0D2240; }
    .header { width: 95%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; }

    .filters { margin: 0 auto 20px; background: white; padding: 15px; border-radius: 8px; display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }

    .container { width: 95%; margin: auto; }
    .card { background: white; border-radius: 12px; overflow: hidden; margin-bottom: 50px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8f9ff; padding: 12px; font-size: 12px; text-transform: uppercase; }
    td { padding: 10px; border-top: 1px solid #eee; font-size: 13px; vertical-align: top; }
</style>

<div class="header">
    <h1>Histórico de Alterações</h1>
    <a href="/bus-companies" class="btn">Voltar</a>
</div>

<div class="container">

    <form method="GET" action="/logs" class="filters">

        <div class="filter-group">
            <label>ID Log</label>
            <input type="number" name="id" value="<?= htmlspecialchars((string)($filters['id'] ?? '')) ?>">
        </div>

        <div class="filter-group">
            <label>Tipo Entidade</label>
            <input type="text" name="entity_type"
                   value="<?= htmlspecialchars((string)($filters['entity_type'] ?? '')) ?>">
        </div>

        <div class="filter-group">
            <label>ID Entidade</label>
            <input type="number" name="entity_id"
                   value="<?= htmlspecialchars((string)($filters['entity_id'] ?? '')) ?>">
        </div>

        <div class="filter-group">
            <label>ID Usuário</label>
            <input type="number" name="user_id"
                   value="<?= htmlspecialchars((string)($filters['user_id'] ?? '')) ?>">
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

        <button type="submit">Filtrar</button>
    </form>

    <div class="card">

        <?php if (empty($logs)): ?>
            <div style="padding:20px;">Nenhum log encontrado.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Entidade</th>
                    <th>ID Entidade</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Antes</th>
                    <th>Depois</th>
                    <th>Data</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($logs as $log): ?>

                    <?php
                    $old = !empty($log['old_value'])
                            ? json_decode((string)$log['old_value'], true)
                            : [];

                    $new = !empty($log['new_value'])
                            ? json_decode((string)$log['new_value'], true)
                            : [];

                    $changes = [];

                    if ($log['action'] === 'update' && is_array($old) && is_array($new)) {
                        foreach ($new as $key => $value) {
                            $oldValue = $old[$key] ?? null;

                            if ($oldValue != $value) {
                                $changes[$key] = [
                                        'old' => $oldValue,
                                        'new' => $value
                                ];
                            }
                        }
                    }
                    ?>

                    <tr>
                        <td><?= $log['id'] ?></td>

                        <td><?= htmlspecialchars($log['entity_type'] ?? '-') ?></td>

                        <td><?= $log['entity_id'] ?? '-' ?></td>

                        <td><?= $log['user_id'] ?? '0' ?></td>

                        <td><?= strtoupper($log['action']) ?></td>

                        <td>
                            <?php if ($changes): ?>
                                <?php foreach ($changes as $k => $v): ?>
                                    <div>
                                        <strong><?= $k ?>:</strong>
                                        <?= htmlspecialchars((string)($v['old'] ?? '')) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($changes): ?>
                                <?php foreach ($changes as $k => $v): ?>
                                    <div>
                                        <strong><?= $k ?>:</strong>
                                        <?= htmlspecialchars((string)($v['new'] ?? '')) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td style="white-space: nowrap;">
                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>
                </tbody>

            </table>
        <?php endif; ?>

    </div>
</div>