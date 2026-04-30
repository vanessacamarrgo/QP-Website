<?php
/** @var array{name: string, url: string, city: string, status: string} $old */
?>
<?php
/** @var array $busCompany */
?>

<div class="form-group">
    <label for="logo">Logo</label>
    <input
            type="file"
            id="logo"
            name="logo"
            accept="image/png, image/jpeg"
    >
</div>

<div class="form-group">
    <label for="name">Nome da Viação</label>
    <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($busCompany['name'] ?? '') ?>"
            placeholder="Ex: Viação Cometa"
            required
    >
</div>

<div class="form-group">
    <label for="url">URL do Site</label>
    <input
            type="url"
            id="url"
            name="url"
            value="<?= htmlspecialchars($busCompany['url'] ?? '') ?>"
            placeholder="Ex: https://www.viacaocometa.com.br"
            required
    >
</div>

<div class="form-group">
    <label for="city">Cidade</label>
    <input
            type="text"
            id="city"
            name="city"
            value="<?= htmlspecialchars($busCompany['city'] ?? '') ?>"
            placeholder="Ex: São Paulo"
            required
    >
</div>

<div class="form-group">
    <label for="status">Status</label>
    <select id="status" name="status">
        <option value="active"   <?= ($busCompany['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Ativo</option>
        <option value="inactive" <?= ($busCompany['status'] ?? '')        === 'inactive' ? 'selected' : '' ?>>Inativo</option>
    </select>
</div>


<style>
    /* Reaproveitando os estilos que você já usa no create */
    .header { width: 90%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; display: inline-block; font-weight: bold; transition: all 0.25s ease; }
    .btn:hover { background: #2d5bff; transform: translateY(-2px) scale(1.03); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .btn-cancel { background: gray; }
    .container { width: 90%; max-width: 650px; margin: auto; }
    .form-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .error-list { background: #ffe5e5; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 5px; color: #b91c1c; }
    .form-actions { margin-top: 20px; display: flex; gap: 10px; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-size: 13px; }
    input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
</style>
