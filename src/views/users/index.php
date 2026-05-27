<?php
/** @var string $title */
/** @var list<\App\Models\User> $users */
/** @var array $pagination */
/** @var string $filterName */
/** @var string $filterStatus */
?>

<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h2>Excluir Usuário</h2>
        <p>O registro ficará como <strong>deletado</strong> e poderá ser restaurado depois.</p>
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
        <a href="/bus-companies/logs" class="btn">Historico</a>
        <a href="/bus-companies" class="btn">Viações</a>
        <a href="/users/create" class="btn">+ Novo Usuário</a>
        <a href="/" class="btn">Home</a>
    </div>
</div>

<div class="container">

    <form method="GET" action="/users" class="filters">
        <div class="filter-group">
            <label for="filter-name">Nome</label>
            <input type="text" id="filter-name" name="name"
                   value="<?= htmlspecialchars($filterName) ?>"
                   placeholder="Buscar por nome...">
        </div>

        <div class="filter-group">
            <label for="filter-status">Status</label>
            <select id="filter-status" name="status">
                <option value="">Todos (exceto deletados)</option>
                <option value="active"   <?= $filterStatus === 'active'   ? 'selected' : '' ?>>Ativo</option>
                <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                <option value="deleted"  <?= $filterStatus === 'deleted'  ? 'selected' : '' ?>>Deletados</option>
            </select>
        </div>

        <button type="submit" class="btn-filter">Filtrar</button>

        <?php if (!empty($filterName) || !empty($filterStatus)): ?>
            <a href="/users" class="btn-clear">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="table-card">
        <div class="table-card-header">
            <h3>Lista de Usuários</h3>
            <span><?= $pagination['total'] ?> registro(s)</span>
        </div>

        <?php if (empty($users)): ?>
            <div class="empty">Nenhum usuário encontrado.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->id ?></td>
                        <td><?= htmlspecialchars($user->name) ?></td>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td>
                            <span class="badge badge-<?= $user->status ?>">
                                <?= match($user->status) {
                                    'active'   => 'Ativo',
                                    'inactive' => 'Inativo',
                                    'deleted'  => 'Deletado',
                                    default    => $user->status,
                                } ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($user->createdAt)) ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($user->status !== 'deleted'): ?>
                                    <a href="/users/<?= $user->id ?>/edit" class="edit">Editar</a>
                                    <button type="button" class="delete-btn"
                                            onclick="openDeleteModalCustom('/users/<?= $user->id ?>/delete')">
                                        Excluir
                                    </button>
                                <?php else: ?>
                                    <form method="POST" action="/users/<?= $user->id ?>/restore" style="display:inline">
                                        <button type="submit" class="edit"
                                                style="background:#28a745;color:white;border:none;cursor:pointer;padding:4px 10px;border-radius:4px;">
                                            Restaurar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php
            $baseUrl     = '/users';
            $queryParams = ['name' => $filterName, 'status' => $filterStatus];
            include __DIR__ . '/../partials/pagination.php';
            ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function openDeleteModalCustom(url) {
        const modal = document.getElementById('deleteModal');
        const form  = document.getElementById('deleteConfirmForm');
        form.action = url;
        modal.classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }
</script>