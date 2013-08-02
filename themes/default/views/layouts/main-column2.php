<?php $this->beginContent('//layouts/main'); ?>

    <section class="container sbr clearfix">
        <section id="content" class="two-thirds column">
            <?php echo $content; ?>
        </section>

        <aside id="sidebar" class="one-third column">
            <?php if (!empty($this->clips['sidebar'])): ?>
                <?php echo $this->clips['sidebar']; ?>
            <?php endif; ?>
        </aside>

    </section>

<?php $this->endContent(); ?>