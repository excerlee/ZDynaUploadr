<!--protected/views/site/upload.php-->
<div class="yiiForm">
<?php echo CHtml::form('', 'post', array('enctype'=>'multipart/form-data')); ?>
 
<?php echo CHtml::errorSummary($form); ?>
 
<h3><?php echo $form->title;?></h3>
<hr noshade>

<div class="simple">

<?php foreach ($form->getTextVars() as $var):?>
	<?php echo CHtml::activeLabel($form, $var); ?>
	<?php echo CHtml::activeTextField($form, $var); ?>
	<br/>
<?php endforeach;?>

<?php echo CHtml::activeLabel($form,'upload_file'); ?>
<?php echo CHtml::activeFileField($form, 'upload_file'); ?>
<br/>
<br/>
<?php echo CHtml::submitButton('Upload'); ?>
</div>
 
<?php echo CHtml::endForm(); ?>
 
</div>