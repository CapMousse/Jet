<?php $this->view->beginBlock('content'); ?>
    <?php foreach($articles as $name => $value): ?>
            Article : <?=$value->titre_article?>
    <?php endforeach; ?>
<?php $this->view->endBlock(); ?>
