<?php

class ProfileController extends Controller
{
	public $defaultAction = 'profile';
	public $layout='//layouts/column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
	
	/**
	 * Shows a particular model.
	 */
	public function actionProfile()
	{
		$model = $this->loadUser();
	    $this->render('profile',array(
	    	'model'=>$model,
			'profile'=>$model->profile,
	    ));
	}

	/**
	 * Shows a particular model.
	 */
	public function actionView($id)
	{
		$model = getModule('user')->getUserByName($id);
	    $this->render('profile',array(
	    	'model'=>$model,
			'profile'=>$model->profile,
	    ));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionEdit()
	{
		$model = $this->loadUser();
		$profile=$model->profile;
		
		// ajax validator
		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form')
		{
			echo UActiveForm::validate(array($model,$profile));
			Yii::app()->end();
		}
		
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$profile->attributes=$_POST['Profile'];
			
            $file = CUploadedFile::getInstance($profile, 'avatar');

            if (!empty($file)) {
                $path = 'uploads/avatar/' . user_detail()->username;

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $allowLib = Libraries::model()->checkAllowExt($file->extensionName, 'photo');

                if (!$allowLib) {
                    $msg .= '<div class="error">Unsupported filetype.</div>';
                }

                if ($allowLib) {
                    $file->saveAs($path . '/' . user_detail()->username . '.' . $file->extensionName);
                    $profile->avatar = $path . '/' . user_detail()->username . '.' . $file->extensionName;

                    # Create thumbnail for avatar
                    $width = 50;
                    $height = 50;
                    $fileThumb = Yii::app()->iwi->load($profile->avatar);

                    if ($fileThumb->width > $fileThumb->height) {
                        $master = Image::HEIGHT;
                    } else {
                        $master = Image::WIDTH;
                    }

                    $fileThumb->resize($width,$height, $master)->crop($width,$height, 'center')->save($path . '/thumb-' . user_detail()->username . '.' . $file->extensionName);
                }
            }			

			if($model->validate()&&$profile->validate()) {
				$model->save();
				$a = $profile->save();
                Yii::app()->user->updateSession();
				Yii::app()->user->setFlash('profileMessage',UserModule::t("Changes is saved."));
				$this->redirect(array('/user/profile'));
			} else {
				$profile->validate();
			}
		}

		$this->render('edit',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}
	
	/**
	 * Change password
	 */
	public function actionChangepassword() {
		$model = new UserChangePassword;
		if (Yii::app()->user->id) {
			
			// ajax validator
			if(isset($_POST['ajax']) && $_POST['ajax']==='changepassword-form')
			{
				echo UActiveForm::validate($model);
				Yii::app()->end();
			}
			
			if(isset($_POST['UserChangePassword'])) {
					$model->attributes=$_POST['UserChangePassword'];
					if($model->validate()) {
						$new_password = User::model()->notsafe()->findbyPk(Yii::app()->user->id);
						$new_password->password = UserModule::encrypting($model->password);
						$new_password->activkey=UserModule::encrypting(microtime().$model->password);
						$new_password->save();
						Yii::app()->user->setFlash('profileMessage',UserModule::t("New password is saved."));
						$this->redirect(array("profile"));
					}
			}
			$this->render('changepassword',array('model'=>$model));
	    }
	}

	/**
	 * Car history
	 */
	public function actionCar()
	{
		$model = $this->loadUser();
		$this->render('car', array('model' => $model));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadUser()
	{
		if($this->_model===null)
		{
			if(Yii::app()->user->id)
				$this->_model=Yii::app()->controller->module->user();
			if($this->_model===null)
				$this->redirect(Yii::app()->controller->module->loginUrl);
		}
		return $this->_model;
	}
}