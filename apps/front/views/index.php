<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?=$views->getVar('title')?></title>
	<link type="text/css" media="screen" rel="stylesheet" href="<?=ROOT?>/design/design.css" />
	<?=$views->getBlock('css')?>
	<?=$views->getBlock('meta')?>
</head>

<body>
	<header>
		<h1>Exemple template</h1>
	</header>
	<section id="content">
		<?=$views->getBlock('content')?>	
	</section>
	<?=$views->getBlock('javascript')?>
</body>
</html>