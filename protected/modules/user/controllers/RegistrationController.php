<?php

class RegistrationController extends Controller
{
    public $defaultAction = 'registration';
    
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,
            ),
        );
    }

    # Disable registration by denied all users
    public function filters()
    {
        return CMap::mergeArray(parent::filters(),array(
            'accessControl', // perform access control for CRUD operations
        ));
    }

    public function accessRules()
    {
        return array(
            array('allow',  // allow all guest
                'users'=>array('?'),
            ),
            array('deny',  // deny all login user
                'users'=>array('@'),
            ),
        );
    }   

    /**
     * Registration user
     */
    public function actionRegistration() {
        $model = new RegistrationForm;
        $profile=new Profile;
        $profile->regMode = true;
        
        // ajax validator
        if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
        {
            echo UActiveForm::validate(array($model,$profile));
            Yii::app()->end();
        }
        
        if (Yii::app()->user->id) {
            $this->redirect(Yii::app()->controller->module->profileUrl);
        } else {

            $transaction=$model->dbConnection->beginTransaction();

            if (isset($_GET['provider']) && !empty($_GET['provider'])) {
                # Hybrid Auth
                $ha = getModule('hybridauth')->getHybridAuth();
                $ha_identity = new RemoteUserIdentity($_GET['provider'], $ha);
                $provider = $ha->getAdapter($ha_identity->loginProvider);
                $udetail = $provider->getUserProfile();

                $RegistrationForm['email'] = null;
                $RegistrationForm['username'] = 'driver_' . time();

                $Profile['first_name'] = $udetail->firstName;
                $Profile['last_name'] = $udetail->lastName;
                $Profile['displayname'] = $udetail->displayName;
                $Profile['avatar'] = $udetail->photoURL;
                $Profile['about_me'] = $udetail->description;                

                $model->attributes = $RegistrationForm;
                $profile->attributes = $Profile;
            }

            try {
                if(isset($_POST['RegistrationForm'])) {
                    $model->attributes=$_POST['RegistrationForm'];
                    $profile->attributes=((isset($_POST['Profile'])?$_POST['Profile']:array()));
                    if($model->validate()&&$profile->validate())
                    {
                        $soucePassword = $model->password;
                        $model->activkey=UserModule::encrypting(microtime().$model->password);
                        $model->password=UserModule::encrypting($model->password);
                        $model->verifyPassword=UserModule::encrypting($model->verifyPassword);
                        $model->superuser=0;
                        $model->status=((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);
                        
                        if ($model->save()) {
                            $profile->user_id=$model->id;
                            $profile->save();
                            if (Yii::app()->controller->module->sendActivationMail) {
                                $activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $model->activkey, "email" => $model->email));
                                UserModule::sendMail($model->email,UserModule::t("You registered from {site_name}",array('{site_name}'=>Yii::app()->name)),UserModule::t("Please activate you account go to {activation_url}",array('{activation_url}'=>$activation_url)));
                            }
                            
                            if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
                                $identity=new UserIdentity($model->username,$soucePassword);
                                $identity->authenticate();
                                Yii::app()->user->login($identity,0);

                                $transaction->commit();
                                
                                $this->redirect(absUrl('/user/profile/edit'));
                            } else {
                                if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
                                    Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
                                } elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
                                    Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl))));
                                } elseif(Yii::app()->controller->module->loginNotActiv) {
                                    Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
                                } else {
                                    Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
                                }

                                $transaction->commit();
                                
                                $this->refresh();
                            }
                        }
                    } else $profile->validate();
                }
            } catch (Exception $e) {
                $transaction->rollback();
            }

            $this->render('/user/registration',array('model'=>$model,'profile'=>$profile));
        }
    }
}