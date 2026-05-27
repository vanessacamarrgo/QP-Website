<?php
/** @var \App\Models\BusCompany $company */
/** @var array $logs */
?>

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Sora', sans-serif; }
    body { background: #f5f5f5; margin: 0; }
    .header { width: 95%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    h1 { font-weight: 700; color: #0D2240; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; transition: 0.2s; border: none; cursor: pointer; }
    .btn:hover { background: #2d5bff; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #bb2d3b; }
    .btn-success { background: #28a745; }
    .container { width: 95%; margin: auto; }
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 24px; margin-bottom: 24px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .info-item label { display: block; font-size: 11px; font-weight: bold; color: #888; text-transform: uppercase; margin-bottom: 4px; }
    .info-item p { margin: 0; font-size: 15px; color: #222; }
    .badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    .badge-active   { background: #d4edda; color: #155724; }
    .badge-inactive { background: #fff3cd; color: #856404; }
    .badge-deleted  { background: #f8d7da; color: #721c24; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8f9ff; color: #555; padding: 10px 12px; text-align: left; border: 1px solid #eee; font-size: 12px; text-transform: uppercase; }
    td { padding: 10px 12px; border: 1px solid #eee; font-size: 13px; vertical-align: top; }
    tr:hover { background: #fafbff; }
    h2 { color: #0D2240; font-size: 16px; margin-bottom: 16px; }
    .action-color-create  { color: #28a745; font-weight: bold; }
    .action-color-update  { color: #ffc107; font-weight: bold; }
    .action-color-delete  { color: #dc3545; font-weight: bold; }
    .action-color-restore { color: #17a2b8; font-weight: bold; }
</style>

<div class="header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <div style="display:flex;gap:8px;">
        <?php if ($company->status !== 'deleted'): ?>
            <a href="/bus-companies/<?= $company->id ?>/edit" class="btn">Editar</a>
            <form method="POST" action="/bus-companies/<?= $company->id ?>/delete" style="display:inline">
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Excluir esta viação?')">Excluir</button>
            </form>
        <?php else: ?>
            <form method="POST" action="/bus-companies/<?= $company->id ?>/restore" style="display:inline">
                <button type="submit" class="btn btn-success">Restaurar</button>
            </form>
        <?php endif; ?>
        <a href="/bus-companies" class="btn" style="background:#6c757d;">Voltar</a>
    </div>
</div>

<div class="container">

    <div class="card">
        <div class="info-grid">
            <div class="info-item">
                <label>ID</label>
                <p><?= $company->id ?></p>
            </div>
            <div class="info-item">
                <label>Nome</label>
                <p><?= htmlspecialchars($company->name) ?></p>
            </div>
            <div class="info-item">
                <label>URL</label>
                <p><a href="<?= htmlspecialchars($company->url) ?>" target="_blank"><?= htmlspecialchars($company->url) ?></a></p>
            </div>
            <div class="info-item">
                <label>Cidade</label>
                <p><?= htmlspecialchars($company->city) ?></p>
            </div>
            <div class="info-item">
                <label>Status</label>
                <p><span class="badge badge-<?= $company->status ?>"><?= ucfirst($company->status) ?></span></p>
            </div>
            <div class="info-item">
                <label>Criado em</label>
                <p><?= date('d/m/Y H:i', strtotime($company->createdAt)) ?></p>
            </div>
            <?php if (!empty($company->logo)): ?>
                <div class="info-item">
                    <label>Logo</label>
                    <img src="/<?= htmlspecialchars(ltrim($company->logo, '/')) ?>"
                         style="width:80px;height:80px;object-fit:contain;border:1px solid #eee;border-radius:6px;">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <h2>Histórico de alterações (<?= count($logs) ?>)</h2>

        <?php if (empty($logs)): ?>
            <p style="color:#888;">Nenhuma alteração registrada.</p>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Ação</th>
                    <th>Feito por</th>
                    <th>Antes</th>
                    <th>Depois</th>
                    <th>Data</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <?php
                    $old     = !empty($log['old_value']) ? json_decode((string)$log['old_value'], true) : null;
                    $new     = !empty($log['new_value']) ? json_decode((string)$log['new_value'], true) : null;
                    $changes = [];
                    if ($log['action'] === 'update' && is_array($old) && is_array($new)) {
                        foreach ($new as $k => $v) {
                            if (in_array($k, ['updated_at', 'created_at'])) continue;
                            if (($old[$k] ?? null) != $v) $changes[$k] = ['old' => $old[$k] ?? '-', 'new' => $v];
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $log['id'] ?></td>
                        <td class="action-color-<?= $log['action'] ?>"><?= strtoupper($log['action']) ?></td>
                        <td><?= htmlspecialchars((string)($log['user_name'] ?? 'Sistema')) ?></td>
                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <?php foreach ($changes as $k => $v): ?>
                                    <div><small><strong><?= $k ?>:</strong> <?= htmlspecialchars((string)$v['old']) ?></small></div>
                                <?php endforeach; ?>
                            <?php elseif ($log['action'] === 'delete'): ?>
                                <small style="color:gray">Registro ativo</small>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <?php foreach ($changes as $k => $v): ?>
                                    <div><small><strong><?= $k ?>:</strong> <?= htmlspecialchars((string)$v['new']) ?></small></div>
                                <?php endforeach; ?>
                            <?php elseif ($log['action'] === 'create'): ?>
                                <small style="color:#28a745">Criado</small>
                            <?php elseif ($log['action'] === 'restore'): ?>
                                <small style="color:#17a2b8">Restaurado</small>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>