<?php
/** @var array $busCompany */
?>

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" rel="stylesheet">

<div class="form-group">
    <label for="logo">Logo da Viação</label>
    <input
            type="file"
            id="logo"
            name="logo"
            accept="image/png, image/jpeg"
    >
    <?php if (!empty($busCompany['logo'])): ?>
        <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 8px; border-radius: 6px; border: 1px solid #eee;">
            <img src="/<?= htmlspecialchars($busCompany['logo']) ?>" width="50" height="50" style="object-fit: contain; border-radius: 4px; background: white;">
            <span style="font-size: 12px; color: #666;">Logo atual.</span>
            <input type="hidden" name="old_logo" value="<?= $busCompany['logo'] ?>">
        </div>
    <?php endif; ?>
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
    /* ALTERAÇÃO: Aplicando Sora globalmente e no título */
    * {
        font-family: 'Sora', sans-serif;
    }

    h1 {
        font-family: 'Sora', sans-serif !important;
    }

    /* Resto do seu CSS original sem alterações */
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: bold; color: #555; }
    input, select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 14px;
    }
    input:focus, select:focus {
        border-color: #1a2e6e;
        outline: none;
        box-shadow: 0 0 0 2px rgba(26, 46, 110, 0.1);
    }

    .header { width: 90%; margin: 30px auto 15px; display: flex; justify-content: space-between; align-items: center; }
    .btn { background: #1a2e6e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; display: inline-block; font-weight: bold; transition: all 0.25s ease; }
    .btn:hover { background: #2d5bff; transform: translateY(-2px) scale(1.03); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .btn-cancel {
        background: gray;
        padding: 10px 12px; /* O primeiro número é altura, o segundo é largura lateral */
        font-size: 13px;    /* Opcional: diminui um pouco a letra também */
    }    .container { width: 90%; max-width: 650px; margin: auto; }
    .form-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
</style>