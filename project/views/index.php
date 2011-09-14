<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $this->title ?></title>
    <link type="text/css" media="screen" rel="stylesheet" href="<?php echo Jet::get('web_url') ?>design/design.css" />
    <?php echo $this->getBlock('css')?>
    <?php echo $this->getBlock('meta')?>
</head>

<body>
    <header>
        <h1>Congrats !</h1>
    </header>
    <section id="content">
        <?php echo $this->getBlock('content')?>	
    </section>
    <?php echo $this->getBlock('javascript')?>
</body>
</html>