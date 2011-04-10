<?php $this->view->createBlock('content'); ?>
	<?php foreach($tableau as $name => $value): ?>
		Tableau : <?=$value?><br />
	<?php endforeach; ?>
<?php $this->view->endBlock('content'); ?>
