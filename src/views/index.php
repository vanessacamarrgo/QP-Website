<?php
/** @var string $title */
/** @var list<\App\Models\BusCompany> $companies */
/** @var string $filterName */
/** @var string $filterStatus */
/** @var string $busCompanyNamesJson */
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
<body>

<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h2>Excluir Viação</h2>
        <p>Tem certeza que deseja excluir esta viação?<br>Esta ação não pode ser desfeita.</p>
        <div class="modal-actions">
            <button class="modal-btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
            <form id="deleteConfirmForm" method="POST" action="">
                <button type="submit" class="modal-btn-confirm">Sim, excluir</button>
            </form>
        </div>
    </div>
</div>

<div class="header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <div class="header-actions">
        <a href="/bus-companies/logs" class="btn">Histórico de Alterações</a>
        <a href="/bus-companies/create" class="btn">+ Nova Viação</a>
        <a href="/#" class="btn">Home Page</a>
    </div>
</div>

<div class="container">

    <form method="GET" action="/bus-companies" class="filters">
        <div class="filter-group">
            <label for="filter-name">Nome</label>
            <input
                    type="text"
                    id="filter-name"
                    name="name"
                    value="<?= htmlspecialchars($filterName) ?>"
                    placeholder="Buscar por nome..."
                    autocomplete="off"
            >
            <div class="autocomplete-list" id="autocompleteList"></div>
        </div>

        <div class="filter-group">
            <label for="filter-status">Status</label>
            <select id="filter-status" name="status">
                <option value="">Todos</option>
                <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Ativo</option>
                <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Inativo</option>
            </select>
        </div>

        <button type="submit" class="btn-filter">Filtrar</button>

        <?php if (!empty($filterName) || !empty($filterStatus)): ?>
            <a href="/bus-companies" class="btn-clear">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="table-card">
        <div class="table-card-header">
            <h3>Lista de Viações</h3>
            <span><?= count($companies) ?> registro(s)</span>
        </div>

        <?php if (empty($companies)): ?>
            <div class="empty">Nenhuma viação encontrada.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Nome</th>
                    <th>URL</th>
                    <th>Cidade</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr>
                        <td><?= $company->id ?></td>
                        <td>
                            <?php if (!empty($company->logo)): ?>
                                <?php

                                $cleanPath = ltrim($company->logo, '/');

                                $finalSrc = (strpos($cleanPath, 'uploads/') === 0) ? $cleanPath : 'uploads/' . $cleanPath;
                                ?>
                                <img src="/<?= htmlspecialchars($finalSrc) ?>"
                                     style="width:50px;height:50px;object-fit:contain;border-radius:6px;border:1px solid #eee;">
                            <?php else: ?>

                            <?php endif; ?>
                        </td>
                        <td class="company-name"><?= htmlspecialchars($company->name) ?></td>
                        <td>
                            <a class="company-url" href="<?= htmlspecialchars($company->url) ?>" target="_blank">
                                <?= htmlspecialchars($company->url) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($company->city) ?></td>
                        <td>
                            <span class="badge badge-<?= $company->status ?>">
                                <?= $company->status === 'active' ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($company->createdAt)) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="/bus-companies/<?= $company->id ?>/edit" class="edit">Editar</a>
                                <button
                                        type="button"
                                        class="delete-btn"
                                        onclick="openDeleteModalCustom('/bus-companies/<?= $company->id ?>/delete')">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    window.allNames = <?= $busCompanyNamesJson ?? '[]' ?>;

    // Função auxiliar para o seu script.js conseguir preencher o formulário do modal
    function openDeleteModalCustom(url) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteConfirmForm');
        form.action = url;
        modal.classList.add('active');
    }
</script>

<script src="/script.js"></script>
</body>