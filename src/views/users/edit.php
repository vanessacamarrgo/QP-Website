<?php
/** @var string $title */
/** @var User $user */
/** @var list<string> $errors */
/** @var array $old */

use App\Models\User;

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

    <form method="POST" action="/users/<?= $user->id ?>/update" class="form-card">
        <?php $userData = $old;
        include __DIR__ . '/../partials/form_user.php';
         ?>
    </form>
</div>
