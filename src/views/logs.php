<?php
declare(strict_types=1);
/** @var array $logs */
?>

<style>
    /* Mantenha todo o seu CSS aqui (body, .header, .btn, .card, table, etc.) */
    .header { width: 90%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; }
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8f9ff; padding: 12px; text-align: left; border: 1px solid #eee; }
    td { padding: 10px; border: 1px solid #eee; font-size: 13px; }
    img { border-radius: 6px; object-fit: contain; }
</style>

<div class="header">
    <h1>Histórico de Alterações</h1>
    <a href="/bus-companies" class="btn">Voltar</a>
</div>

<div class="container">
    <div class="card">
        <?php if (empty($logs)): ?>
            <div style="padding:20px;color: #555;">Nenhum log registrado.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
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
                <?php foreach ($logs as $log):
                    $old = !empty($log['old_value']) ? json_decode($log['old_value'], true) : null;
                    $new = !empty($log['new_value']) ? json_decode($log['new_value'], true) : null;
                    $changes = [];

                    if ($log['action'] === 'update' && $old && $new) {
                        foreach ($new as $key => $value) {
                            if (in_array($key, ['updated_at'])) continue;
                            $oldValue = $old[$key] ?? null;
                            if ($oldValue != $value) {
                                $changes[$key] = ['old' => $oldValue, 'new' => $value];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $log['id'] ?></td>
                        <td><?= $log['bus_company_id'] ?></td>
                        <td>
                            <?php
                            $logo = null;
                            if ($log['action'] === 'update') $logo = $new['logo'] ?? $old['logo'] ?? null;
                            elseif ($log['action'] === 'create') $logo = $new['logo'] ?? null;
                            elseif ($log['action'] === 'delete') $logo = $old['logo'] ?? null;
                            ?>
                            <?php if ($logo): ?>
                                <img src="/<?= $logo ?>" width="50">
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            if ($log['action'] === 'delete' && $old) {
                                echo htmlspecialchars($old['name'] ?? 'Removida');
                            } else {
                                echo htmlspecialchars($log['name'] ?? ($new['name'] ?? 'Removida'));
                            }
                            ?>
                        </td>
                        <td><?= strtoupper($log['action']) ?></td>
                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <ul>
                                    <?php foreach ($changes as $key => $c): ?>
                                        <li><strong><?= $key ?>:</strong>
                                            <?php if ($key === 'logo' && $c['old']): ?>
                                                <br><img src="/<?= $c['old'] ?>" width="50">
                                            <?php else: ?>
                                                <?= htmlspecialchars((string)($c['old'] ?? '-')) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <ul>
                                    <?php foreach ($changes as $key => $c): ?>
                                        <li><strong><?= $key ?>:</strong>
                                            <?php if ($key === 'logo' && $c['new']): ?>
                                                <br><img src="/<?= $c['new'] ?>" width="50">
                                            <?php else: ?>
                                                <?= htmlspecialchars((string)($c['new'] ?? '-')) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>