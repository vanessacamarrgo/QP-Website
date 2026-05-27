<?php
/** @var string $title */
/** @var list<string> $errors */
/** @var array $old */
?>

<div class="header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="/users" class="btn">Voltar</a>
</div>

<div class="container">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/users" class="form-card">
        <?php $user = $old;
        include __DIR__ . '/../partials/form_user.php';
        ?>
    </form>
</div>