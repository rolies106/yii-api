<?php

class FrontpageController extends Controller
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return CMap::mergeArray(parent::filters(),array(
            'accessControl', // perform access control for CRUD operations
        ));
    }
    
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('settings', 'pollings', 'recent','following','active','favorites', 'loginopenid', 'authenticate', 'facebook', 'twitter', 'getmember'),
                'users'=>array('@'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

	public function actionIndex()
	{
		$this->render('index');
	}

    public function actionGetmember()
    {
        if (request()->isAjaxRequest) {
            $return = getModule('user')->userFilterByName($_GET['q']);

            $users = array();
            if (!empty($return)) {
                foreach ($return as $user) {
                    $users[] = array('value' => $user->user_id, 'name' => $user->firstname);
                }
            }

            echo json_encode($users);
            die();
        } else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }

    public function actionSettings($username)
    {   
        $model = Yii::app()->controller->module->user();
        $profile = Profile::model()->findByPk(user_detail()->id);
        $msg = '';

        if (isset($_POST['user'])) {

            if (!isset($_POST['user']['email']) || empty($_POST['user']['email'])) {
                $return['msg'] = htmlentities("<div class='errorSummary'>Email can not be empty.</div>");
                Yii::app()->user->setFlash('msg', $return['msg']);
                $return['status'] = false;                

                if (request()->isAjaxRequest) {
                    echo json_encode($return);
                    die();
                }

            } else {

                if ($model->email != $_POST['user']['email']) {
                    $updateMail = true;

                    $criteria = new CDbCriteria;
                    $criteria->condition = 'email = :email';
                    $criteria->params = array(':email' => $_POST['user']['email']);
                    $users = User::model()->findAll($criteria);

                    if (!empty($users)) {
                        $updateMail = false;
                        $msg .= "<div class='error'>Your email has been used by another member.</div>";
                    }
                    
                } else {
                    $updateMail = false;
                }

                $prevEmail = $model->email;

                $model->attributes = $_POST['user'];

                if ($updateMail == false) {
                    $model->email = $prevEmail;
                }

                $saved = $model->save();

                if ($saved && $updateMail) {
                    UserOptions::model()->setOption('mail_verify', false, $model->id);
                    $getRowUser = User::model()->notsafe()->findByPk($model->id);

                    $body = "Please verify your email address by clicking this link : \n";
                    $body .= "<a href='" . absUrl('/user/activation/emailverification', array('activkey' => $getRowUser->activkey, 'email' => $model->email)) . "'>" . absUrl('/user/activation/emailverification', array('activkey' => $getRowUser->activkey, 'email' => $model->email)) . "</a>";

                    $body = wordwrap($body, 70);
                    $body = str_replace("\n.", "\n..", $body);
                    $subject = Yii::app()->name . ' [Email Verification]';

                    # Send verification email
                    app()->rmail->sendEmail($_POST['user']['email'], $body, array('subject' => $subject));
                }

                $return['msg'] = htmlentities("<div class='success'>Your profile has been updated.</div>" . $msg);
                $return['status'] = true;                
            }
        }

        if (isset($_POST['profile'])) {
            if (request()->isAjaxRequest) {
                $this->layout = 'ajax';
            }

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

            $profile->firstname = $_POST['profile']['firstName'];
            $profile->lastname = $_POST['profile']['lastName'];
            $profile->bio = $_POST['profile']['bio'];

            if($profile->update()) {
                $return['msg'] = htmlentities('<div class="success">Your profile has been updated.</div>' . $msg);
                Yii::app()->user->setFlash('msg', $return['msg']);
                $return['status'] = true;
            } else {
                $return['status'] = false;
            }

            if (request()->isAjaxRequest) {
                echo json_encode($return);
                die();                
            }
        }

        $this->render('settings', array('model' => $model, 'profile' => $profile));
    }

    protected function _checkUserExists($email) {
        $criteria = new CDbCriteria;

        $criteria->condition = "email = '" . $email . "'";
        $row = User::model()->find($criteria);

        return $row;
    }

    protected function _checkUserNameExists($username, $service) {
        $criteria = new CDbCriteria;

        $criteria->condition = "username = '" . $username . "' AND provider = '" . $service . "'";
        $row = User::model()->find($criteria);

        return $row;
    }

    protected function _autoLogin($user) {
        $identity=new UserAppsIdentity($user->username,$user->password);
        $identity->authenticate();
        if ( $identity->errorCode == UserIdentity::ERROR_NONE ) {
            $duration= 3600*24*30; // 30 days
            Yii::app()->user->login($identity,$duration);
            // $this->redirect(Yii::app()->createUrl("/profile/".$identity->username));
        } else {
            echo $identity->errorCode;
        }        
    }

    protected function _simpleReg($identity, $service) {
        $userCriteria = new CDbCriteria;

        $mailExplode = (isset($identity->email)) ? explode('@', $identity->email) : $identity->username;
        $username = (isset($identity->username)) ? $identity->username : $mailExplode[0];

        $userCriteria->condition = 'username = :username';
        $userCriteria->params = array(':username' => $username);

        $userRow = User::model()->find($userCriteria);

        if (!empty($userRow)) {
            $username = $username . rand();
        }

        $model = new RegistrationForm;

        # Simple Registrations For User
        $soucePassword = (isset($identity->email)) ? $mailExplode[0] . rand() : $identity->username . rand();
        $model->email=(isset($identity->email)) ? $identity->email : NULL;
        $model->username= $username;
        $model->activkey=UserModule::encrypting(microtime().$soucePassword);
        $model->password=UserModule::encrypting($soucePassword);
        $model->verifyPassword=UserModule::encrypting($soucePassword);
        $model->createtime=time();
        $model->lastvisit=time();
        $model->superuser=0;
        $model->provider=$service;
        $model->status=User::STATUS_ACTIVE;

        if ($model->save()) {
            $profile = new Profile;
            $profile->user_id = $model->id;

            $profile->firstname = (isset($identity->name)) ? $identity->name : $username;
            $profile->birthday = NULL;
            $profile->bio = NULL;
            $profile->avatar = (isset($identity->photo)) ? $identity->photo : NULL;

            $profile->save();

            $this->_updateToken($model->id, $service, $identity);
        }
    }

    protected function _updateToken($user_id, $service, $identity) {

        $criteria = new CDbCriteria;

        $criteria->condition = 'user_id = :userID AND oauth_provider = :service';
        $criteria->params = array(':userID' => $user_id, ':service' => $service);

        $row = UserConnects::model()->find($criteria);

        if (empty($row)) {
            $userConnects = new UserConnects;

            $userConnects->id = NULL;
            $userConnects->user_id = $user_id;
            $userConnects->oauth_provider = $service;
            $userConnects->oauth_uid = $identity->id;
            $userConnects->oauth_token = (isset($identity->access_token)) ? $identity->access_token : NULL;
            
            $userConnects->save();
        } else {
            $row->oauth_token = (isset($identity->access_token)) ? $identity->access_token : NULL;
            $row->update();
        }
    }
}