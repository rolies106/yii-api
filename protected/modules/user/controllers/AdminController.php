<?php

class AdminController extends Controller
{
	public $defaultAction = 'admin';
	// public $layout='//layouts/column2';
	
	private $_model;
	private $_role_model;
	private $_authorizer;

	public function init()
	{
		$this->_authorizer = getModule('rights')->getAuthorizer();
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(),array(
			'accessControl', // perform access control for CRUD operations
		));
	}
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin', 'view'),
				'roles'=>array('View Employee'),
			),
			array('allow',
				'actions'=>array('create', 'update'),
				'roles'=>array('Create Employee'),
			),
			array('allow',
				'actions'=>array('delete'),
				'roles'=>array('Delete Employee'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update','view', 'roles', 'subordinate'),
				'users'=>UserModule::getAdmins(),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['User']))
            $model->attributes=$_GET['User'];

        $this->render('index',array(
            'model'=>$model,
        ));
		/*$dataProvider=new CActiveDataProvider('User', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->controller->module->user_page_size,
			),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));//*/
	}


	/**
	 * Manage user roles
	 */
	public function actionRoles($user_id)
	{
		$authorizer = Yii::app()->getModule("rights")->authorizer;

		# Save all user roles
		if (Yii::app()->request->isPostRequest) {

			# Get user role for revoking all permission before assign it
		 	$user_roles = $authorizer->getAuthItems(CAuthItem::TYPE_OPERATION, $user_id);
		 	$assign_role = array();

		 	# Revoke all assigment
		 	if (!empty($user_roles)) {
			 	foreach ($user_roles as $key => $value) {
					$authorizer->authManager->revoke($value->name, $user_id);
			 	}	 		
		 	}

			if (!empty($_POST['user_ops'])) {
				foreach ($_POST['user_ops'] as $user_ops) {
					$authorizer->authManager->assign($user_ops, $user_id);
				}
			}

			$this->redirect(absUrl('/employees'));
		}	

		# Get user role
	 	$user_roles = $authorizer->getAuthItems(CAuthItem::TYPE_OPERATION, $user_id);
	 	$assign_role = array();

	 	if (!empty($user_roles)) {
		 	foreach ($user_roles as $key => $value) {
		 		$assign_role[$value->name] = $value->name;
		 	}	 		
	 	}

		$user = user_detail($user_id);
		$roles = array();

		# Get All Roles
		$tasks = new RAuthItemDataProvider('tasks', array(
			'type'=>CAuthItem::TYPE_TASK,
		));
	    
	    $dataTasks = $tasks->fetchData();

	    # Add all roles to two dimensional array
		foreach ($dataTasks as $data) {
			$roles[$data->name] = array();

			$task = $this->loadRoleModel($data->name);

			// $operations = new RAuthItemDataProvider('operations', array(
			// 	'type' => CAuthItem::TYPE_OPERATION,
			// 	'parent' => $task
			// ));
			
			$operations = new RAuthItemChildDataProvider($task, array(
				'type' => CAuthItem::TYPE_OPERATION
			));
		    
		    $dataOps = $operations->fetchData();

		    foreach ($dataOps as $operation) {
		    	$roles[$data->name][$operation->name] = $operation->name;
		    }
		}

		$this->render('roles',array('user' => $user, 'roles' => $roles, 'assign_role' => $assign_role));
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$model = $this->loadModel();
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;
		$profile=new Profile;
		$this->performAjaxValidation(array($model,$profile));
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->activkey=Yii::app()->controller->module->encrypting(microtime().$model->password);
			$profile->attributes=$_POST['Profile'];
			$profile->user_id=0;
			if($model->validate()&&$profile->validate()) {
				$model->password=Yii::app()->controller->module->encrypting($model->password);
				if($model->save()) {
					$profile->user_id=$model->id;

					if(Yii::app()->user->isSuperuser)
					    $type=$_POST['Type'];
					else
					    $type='User';

					$authorizer = Yii::app()->getModule("rights")->authorizer;
					$authorizer->authManager->assign($type, $model->id);
					
					$profile->save();
				}
				$this->redirect(array('view','id'=>$model->id));
			} else $profile->validate();
		}

		$this->render('create',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();
		$profile=$model->profile;
		$this->performAjaxValidation(array($model,$profile));
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$profile->attributes=$_POST['Profile'];
			
			if($model->validate()&&$profile->validate()) {
				$old_password = User::model()->notsafe()->findByPk($model->id);
				if ($old_password->password!=$model->password) {
					$model->password=Yii::app()->controller->module->encrypting($model->password);
					$model->activkey=Yii::app()->controller->module->encrypting(microtime().$model->password);
				}
				$model->save();
				$profile->save();
				$this->redirect(array('view','id'=>$model->id));
			} else $profile->validate();
		}

		$this->render('update',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel();
			$profile = Profile::model()->findByPk($model->id);
			$profile->delete();
			$model->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('/user/admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	/**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($validate)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
        {
            echo CActiveForm::validate($validate);
            Yii::app()->end();
        }
    }
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel($uid = NULL)
	{
		if($this->_model===null)
		{
			if(!empty($uid))
				$this->_model=User::model()->notsafe()->findbyPk($uid);
			if(isset($_GET['id']))
				$this->_model=User::model()->notsafe()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	* START ROLES
	*/

	public function loadRoleModel($itemName)
	{
		if( $itemName!==null )
		{
			$this->_role_model = $this->_authorizer->authManager->getAuthItem($itemName);
			$this->_role_model = $this->_authorizer->attachAuthItemBehavior($this->_role_model);
		}

		if( $this->_role_model===null )
			throw new CHttpException(404, Rights::t('core', 'The requested page does not exist.'));

		return $this->_role_model;
	}

	/**
	* END ROLES
	*/	
}