<?php $this->view->beginBlock('content'); ?>
<form method="post">
    <input type="text" name="name" /><br/>
    <input type="mail" name="mail" /><br/>
    <input type="password" name="pass" /><br/>
    <input type="submit" value="envoyer" />
</form>
<?php $this->view->endBlock(); ?>
