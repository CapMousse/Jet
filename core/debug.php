<table style="margin: auto; background-color: white; border: 1px solid black; width: 400px; position: fixed; top: 0px; left: 0px; ">
	<tr style="border-bottom: 1px solid black; ">
		<td>
			<strong>Execution time:</strong>
		</td>
		<td>
			<?=$debug['endRender'] - $debug['startRender']?>
		</td>
	</tr>
	<tr style="border-bottom: 1px solid black;">
		<td>
			<strong>Current route:</strong>
		</td>
		<td>
			<?=$debug['route']?>
		</td>
	</tr>
	<tr style="border-bottom: 1px solid black;">
		<td>
			<strong>Layout:</strong>
		</td>
		<td>
			<?=$debug['layout']?>
		</td>
	</tr>
	<tr style="border-bottom: 1px solid black;">
		<td>
			<strong>Controllers:</strong>
		</td>
		<td>
			<?php foreach($debug['loadedControllers'] as $controller): ?>
				<?=$controller?><br />
			<?php endforeach; ?>
		</td>
	</tr>
	<tr style="border-bottom: 1px solid black;">
		<td>
			<strong>Views:</strong>
		</td>
		<td>
			<?php foreach($debug['loadedViews'] as $views): ?>
				<?=$views?><br />
			<?php endforeach; ?>
		</td>
	</tr>
	<tr style="border-bottom: 1px solid black;">
		<td>
			<strong>Models:</strong>
		</td>
		<td>
			<?php foreach($debug['loadedModels'] as $model): ?>
				<?=$model?><br />
			<?php endforeach; ?>
		</td>
	</tr>
	<tr style="border-bottom: 1px solid black;">
		<td>
			<strong>Modules:</strong>
		</td>
		<td>
			<?php foreach($debug['loadedModules'] as $module): ?>
				<?=$module?><br />
			<?php endforeach; ?>
		</td>
	</tr>
</table>