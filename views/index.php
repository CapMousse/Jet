<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $this->title ?></title>
    <link type="text/css" media="screen" rel="stylesheet" href="<?php echo Jet::get('web_url') ?>design/design.css" />
    <?php echo $this->view->getBlock('css')?>
    <?php echo $this->view->getBlock('meta')?>
</head>

<body>
    <header>
        <h1>Congrats !</h1>
    </header>
    <section id="content">
        <?php echo $this->view->getBlock('content')?>	
    </section>
    <?php echo $this->view->getBlock('javascript')?>
</body>
</html>