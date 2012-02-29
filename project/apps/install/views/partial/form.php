<?php $this->beginBlock('content'); ?>
<h2>Contact form example :</h2>
<form method="post">
    Name : <input type="text" name="name" /><br/>
    Mail <input type="mail" name="mail" /><br/>
    <?php echo $this->getCSRF() ?>
    <input type="submit" value="envoyer" />
</form>
<?php $this->endBlock(); ?>
