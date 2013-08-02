<?php
$this->breadcrumbs=array(
	(UserModule::t('Users'))=>array('admin'),
	$model->username=>array('view','id'=>$model->id),
	(UserModule::t('Update')),
);
$this->menu=array(
    array('label'=>UserModule::t('Add New Employee'), 'url'=>array('create')),
    array('label'=>UserModule::t('All Employee'), 'url'=>array('/employees')),
    // array('label'=>UserModule::t('Manage Profile Field'), 'url'=>array('profileField/admin')),
);
?>

<h1><?php echo  UserModule::t('Update User')." ".$model->id; ?></h1>

<?php
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile));
?>