<?php
declare(strict_types=1);

/** @var array $company */
/** @var list<string> $errors */
/** @var array $old */

$companyData = (array) ($company ?? []);
?>

<div class="header">
    <h1>Editar Viação #<?= htmlspecialchars((string)($companyData['id'] ?? '')) ?></h1>
    <a href="/bus-companies" class="btn">Voltar</a>
</div>

<div class="container">
    <form method="POST" action="/bus-companies/<?= $company->id ?>/update" enctype="multipart/form-data" class="form-card">
        <?php
        $busCompany = !empty($old) ? $old : (array) $company;
        include __DIR__ . '/partials/form.php';
        ?>
        <div class="form-actions">
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="/bus-companies" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>