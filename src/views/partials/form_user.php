<?php
/** @var array $user       dados do formulário (old ou (array)$userObj) */
/** @var bool  $isEdit     true quando for edição */
$isEdit = !empty($user->id);
?>

<div class="form-group">
    <label for="name">Nome do Usuário</label>
    <input type="text" id="name" name="name"
           value="<?= htmlspecialchars($user['name'] ?? '') ?>"
           placeholder="Ex: Maria" required>
</div>

<div class="form-group">
    <label for="email">E-mail do Usuário</label>
    <input type="email" id="email" name="email"
           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
           placeholder="Ex: maria@gmail.com" required>
</div>

<div class="form-group">
    <label for="password">Senha<?= $isEdit ? ' <small>(deixe em branco para não alterar)</small>' : '' ?></label>
    <input type="password" id="password" name="password"
           placeholder="Mínimo 6 caracteres"
        <?= $isEdit ? '' : 'required' ?>>
</div>

<div class="form-group">
    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="active"   <?= ($user['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Ativo</option>
        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
        <option value="deleted"  <?= ($user['status'] ?? '') === 'deleted'  ? 'selected' : '' ?>>Deletado</option>
    </select>
</div>

<div class="form-actions">
    <button type="submit" class="btn">Salvar</button>
    <a href="/users" class="btn btn-cancel">Cancelar</a>
</div>