<?php
$this->breadcrumbs=array(
	'ZDynaUpload',
);?>

<?php 
if (isset($count) && ($count>0))
{
	printf("Number of records updated %s", $count);
}

?>
<h3>List of Upload Forms available:</h3>

<p>
<?php foreach($actions as $action):?>
	<a href="/index.php?r=zdynaupload/<?php echo $action;?>"><?php echo $action;?></a>
<?php endforeach;?>
</p>
