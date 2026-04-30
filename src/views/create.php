<?php
declare(strict_types=1);
/** @var list<string> $errors */
?>

<div class="header">
    <h1>Nova Viação</h1>
    <a href="/bus-companies" class="btn">Voltar</a>
</div>

<div class="container">
    <form method="POST" action="/bus-companies" enctype="multipart/form-data" class="form-card">
        <?php
        $busCompany = $old ?? [];
        include __DIR__ . '/partials/form.php';
        ?>
        <div class="form-actions">
            <button type="submit" class="btn">Salvar</button>
            <a href="/bus-companies" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>