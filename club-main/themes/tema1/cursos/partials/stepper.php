<?php
$steps = $steps ?? [];
$active = $active ?? 0;
?>

<?php if ($steps) : ?>
    <ul class="win-steps">
        <?php foreach ($steps as $index => $label) : ?>
            <li class="<?= $index === $active ? 'active' : '' ?>">
                <span class="step-index"><?= $index + 1; ?></span>
                <strong><?= $label; ?></strong>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
