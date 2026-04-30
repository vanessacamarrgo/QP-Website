<?php
declare(strict_types=1);

/** @var array $logs */ // Recebido do BusCompanyController

// Função auxiliar interna para garantir que o caminho da logo sempre aponte para /uploads/
$formatLogo = function($path) {
    if (!$path) return null;
    $path = ltrim($path, '/');
    // Se o caminho já não começar com uploads/, nós adicionamos
    return (strpos($path, 'uploads/') === 0) ? '/' . $path : '/uploads/' . $path;
};
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
        margin: 0;
    }

    /* HEADER */
    .header {
        width: 90%;
        margin: 30px auto 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* BOTÃO */
    .btn {
        background: #1a2e6e;
        color: white;
        padding: 10px 16px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: all 0.25s ease;
    }

    .btn:hover {
        background: #2d5bff;
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .btn:active {
        transform: scale(0.97);
    }

    /* CONTAINER */
    .container {
        width: 90%;
        margin: auto;
    }

    /* CARD */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden;
        margin-bottom: 50px;
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #f8f9ff;
        color: #555;
        padding: 12px;
        text-align: left;
        border: 1px solid #eee;
    }

    td {
        padding: 10px;
        border: 1px solid #eee;
        font-size: 13px;
        vertical-align: top;
    }

    tr:hover {
        background: #fafbff;
    }

    /* IMAGEM */
    img {
        border-radius: 6px;
        object-fit: contain;
        border: 1px solid #f0f0f0;
        background: #fff;
    }

    ul {
        padding-left: 20px;
        margin: 0;
    }
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
                        <td><?= $log['bus_company_id'] ?? '<small style="color:gray">Removido</small>' ?></td>

                        <td>
                            <?php
                            $logoField = null;
                            if ($log['action'] === 'update') $logoField = $new['logo'] ?? $old['logo'] ?? null;
                            elseif ($log['action'] === 'create') $logoField = $new['logo'] ?? null;
                            elseif ($log['action'] === 'delete') $logoField = $old['logo'] ?? null;

                            $logoUrl = $formatLogo($logoField);
                            ?>

                            <?php if ($logoUrl): ?>
                                <img src="<?= $logoUrl ?>" width="50" height="50">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php
                            if ($log['action'] === 'delete' && $old) {
                                echo "<strong>" . htmlspecialchars($old['name'] ?? 'Removida') . "</strong>";
                            } else {
                                echo "<strong>" . htmlspecialchars($log['name'] ?? ($new['name'] ?? 'Removida')) . "</strong>";
                            }
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
                                        <li>
                                            <strong><?= $key ?>:</strong>
                                            <?php if ($key === 'logo' && $c['old']): ?>
                                                <br><img src="<?= $formatLogo($c['old']) ?>" width="40">
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
                                        <li>
                                            <strong><?= $key ?>:</strong>
                                            <?php if ($key === 'logo' && $c['new']): ?>
                                                <br><img src="<?= $formatLogo($c['new']) ?>" width="40">
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