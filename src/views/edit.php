<?php
declare(strict_types=1);

/** @var array $company */ // O Controller deve enviar os dados da viação aqui
/** @var list<string> $errors */
/** @var array $old */

// Se o controller enviou como objeto, convertemos para facilitar o uso no partial
$companyData = (array) ($company ?? []);
?>

<div class="header">
    <h1>Editar Viação #<?= htmlspecialchars((string)($companyData['id'] ?? '')) ?></h1>
    <a href="/bus-companies" class="btn">Voltar</a>
</div>

<div class="container">
    <form method="POST" action="/bus-companies/<?= $company->id ?>/update" enctype="multipart/form-data" class="form-card">
        <?php
        // Se houver erro de validação, usa o 'old'. Se não, usa os dados do banco.
        $busCompany = !empty($old) ? $old : (array) $company;
        include __DIR__ . '/partials/form.php';
        ?>
        <div class="form-actions">
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="/bus-companies" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>