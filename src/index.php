<?php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_name = trim($_POST['recipe_name']);
    $instructions = trim($_POST['instructions']);

    if (!empty($recipe_name) && !empty($instructions)) {
        $stmt = $pdo->prepare("
            INSERT INTO recipes (recipe_name, instructions)
            VALUES (?, ?)
        ");
        $stmt->execute([$recipe_name, $instructions]);
    }

    header("Location: index.php");
    exit;
}

try {
    $recipes = $pdo->query("
        SELECT *
        FROM recipes
        ORDER BY created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $recipes = [];
    $error_msg = "Неуспешно извличане на рецептите.";
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вкусни Рецепти - Сподели кулинарна идея</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

<div class="container">

    <h1>🍳 Моята Кухня</h1>
    <p class="subtitle">Споделете любима рецепта или намерете вдъхновение за вечеря</p>

    <form method="POST" action="index.php">
        <input
            type="text"
            name="recipe_name"
            placeholder="Име на ястието (напр. Домашна Мусака)"
            required
        >

        <textarea
            name="instructions"
            placeholder="Необходими продукти и начин на приготвяне..."
            required
        ></textarea>

        <button type="submit">Добави Рецепта</button>
    </form>

    <div class="recipes-feed">
        <?php if (isset($error_msg)): ?>
            <div class="error-msg"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php if (empty($recipes)): ?>
            <p class="no-data">Все още няма добавени рецепти. Бъдете първия готвач!</p>
        <?php else: ?>
            <?php foreach($recipes as $recipe): ?>
                <div class="recipe-card">
                    <h3>🍲 <?= htmlspecialchars($recipe['recipe_name']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
                    <?php if (!empty($recipe['created_at'])): ?>
                        <span class="recipe-meta">Добавено на: <?= htmlspecialchars($recipe['created_at']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
