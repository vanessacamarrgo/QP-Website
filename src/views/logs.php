<?php
declare(strict_types=1);

/** @var array $logs */ // Recebido do BusCompanyController

// Função auxiliar para formatar a logo
$formatLogo = function($path) {
    if (!$path) return null;
    $path = ltrim($path, '/');
    return (strpos($path, 'uploads/') === 0) ? '/' . $path : '/uploads/' . $path;
};
?>

<style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; }
    .header { width: 90%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; transition: all 0.2s; }
    .btn:hover { background: #2d5bff; }
    .container { width: 90%; margin: auto; }
    .card { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); overflow: hidden; margin-bottom: 50px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8f9ff; color: #555; padding: 12px; text-align: left; border: 1px solid #eee; }
    td { padding: 10px; border: 1px solid #eee; font-size: 13px; vertical-align: top; }
    tr:hover { background: #fafbff; }
    img { border-radius: 6px; object-fit: contain; border: 1px solid #f0f0f0; background: #fff; }
    ul { padding-left: 20px; margin: 0; }
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
                <?php foreach ($logs as $log): ?>
                    <?php
                    $old = !empty($log['old_value']) ? json_decode($log['old_value'], true) : null;
                    $new = !empty($log['new_value']) ? json_decode($log['new_value'], true) : null;
                    $changes = [];

                    if ($log['action'] === 'update' && $old && $new) {
                        foreach ($new as $key => $value) {
                            if (in_array($key, ['updated_at', 'created_at'])) continue;
                            $oldValue = $old[$key] ?? null;
                            if ($oldValue != $value) {
                                $changes[$key] = ['old' => $oldValue, 'new' => $value];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td><?= $log['id'] ?></td>

                        <td>
                            <?php
                            // Tenta o apelido do SQL, senão tenta colunas padrão, senão busca no JSON
                            $idViacao = $log['log_bus_id'] ?? ($log['bus_company_id'] ?? ($new['id'] ?? ($old['id'] ?? '-')));
                            echo $idViacao;
                            ?>
                        </td>

                        <td>
                            <?php
                            $logoField = null;
                            if ($log['action'] === 'update') $logoField = $new['logo'] ?? ($old['logo'] ?? null);
                            elseif ($log['action'] === 'create') $logoField = $new['logo'] ?? null;
                            elseif ($log['action'] === 'delete') $logoField = $old['logo'] ?? null;

                            $logoUrl = $formatLogo($logoField);
                            ?>
                            <?php if ($logoUrl): ?>
                                <img src="<?= $logoUrl ?>" width="50" height="50">
                            <?php else: ?> - <?php endif; ?>
                        </td>

                        <td>
                            <?php
                            // Se o nome via JOIN for nulo (viação deletada), busca no JSON
                            $nome = $log['name'] ?? ($new['name'] ?? ($old['name'] ?? 'Viação Removida'));
                            echo "<strong>" . htmlspecialchars((string)$nome) . "</strong>";
                            ?>
                        </td>

                        <td>
                            <span style="font-weight: bold; color: <?= $log['action'] === 'delete' ? '#dc3545' : ($log['action'] === 'create' ? '#28a745' : '#ffc107') ?>;">
                                <?= strtoupper($log['action']) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <ul>
                                    <?php foreach ($changes as $key => $c): ?>
                                        <li><strong><?= $key ?>:</strong> <?= htmlspecialchars((string)($c['old'] ?? '-')) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif ($log['action'] === 'delete' && $old): ?>
                                <small style="color:gray">Dados removidos (ID: <?= $old['id'] ?? $idViacao ?>)</small>
                            <?php else: ?> - <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($log['action'] === 'update' && $changes): ?>
                                <ul>
                                    <?php foreach ($changes as $key => $c): ?>
                                        <li><strong><?= $key ?>:</strong> <?= htmlspecialchars((string)($c['new'] ?? '-')) ?></li>
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