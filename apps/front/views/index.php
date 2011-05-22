<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?=$this->view->getVar('title')?></title>
    <link type="text/css" media="screen" rel="stylesheet" href="<?=ROOT?>/design/design.css" />
    <?=$this->view->getBlock('css')?>
    <?=$this->view->getBlock('meta')?>
</head>

<body>
    <header>
            <h1>Exemple template</h1>
    </header>
    <section id="content">
            <?=$this->view->getBlock('content')?>	
    </section>
    <?=$this->view->getBlock('javascript')?>
</body>
</html>