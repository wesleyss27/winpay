<nav class="win-breadcrumb mb-3">
    <?php if (!empty($items) && is_array($items)) : ?>
        <?php foreach ($items as $index => $crumb) : ?>
            <?php if (!empty($crumb['url']) && $index !== array_key_last($items)) : ?>
                <a href="<?= $crumb['url']; ?>"><?= $crumb['label']; ?></a>
                <span>•</span>
            <?php else : ?>
                <span><?= $crumb['label']; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</nav>
