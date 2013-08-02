<?php

class RegistrationSocialController extends Controller
{
    public $defaultAction = 'registration';
    
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
        if (isset($_GET['provider']) && !empty($_GET['provider'])) {

            # Check allowed provider
            if (strtolower($_GET['provider']) != 'facebook'
                && strtolower($_GET['provider']) != 'twitter') {
                Yii::app()->user->setFlash('error', "We assure you that you pick wrong provider.");
                $this->redirect(Yii::app()->session['urlReferrer'], true);
            }

            # Hybrid Auth
            $ha = getModule('hybridauth')->getHybridAuth();
            $ha_identity = new RemoteUserIdentity($_GET['provider'], $ha);
            $provider = $ha->getAdapter($ha_identity->loginProvider);
            $udetail = $provider->getUserProfile();

            if (isset($udetail->email)) {
                $providerData['email'] = $udetail->email;
                $email = explode('@', $udetail->email);
                $providerData['username'] = reset($email) . time();
            } else {
                $providerData['email'] = null;
                $providerData['username'] = 'gorgeous_' . time();
            }

            # Redirect to manual registration page because email is empty
            if (empty($providerData['email'])) {
                $this->redirect(absUrl('/user/registration', array('provider' => $_GET['provider'])));
            }

            $providerData['first_name'] = $udetail->firstName;
            $providerData['last_name'] = $udetail->lastName;
            $providerData['displayname'] = $udetail->displayName;
            $providerData['avatar'] = $udetail->photoURL;
            $providerData['about_me'] = $udetail->description;

            # Add to user table
            $users = User::model()->getOrAddUsers($providerData['email'], $providerData);

            $providertoken = array('user_id' => $users->id,
                                   'login_provider' => $_GET['provider'],
                                   'login_provider_identifier' => $udetail->identifier,
                                   'token' => $provider->getAccessToken());

            # Save provider token
            $this->_linkProvider($providertoken);

            # Logged in user
            $identity_log=new UserIdentity($providerData['email'], NULL);
            $identity_log->social_login = $ha_identity;
            $identity_log->authenticate();
            Yii::app()->user->login($identity_log, 3600*24);

            Yii::app()->user->setFlash('success', "Welcome, " . $udetail->displayName . ".");

            if ($users->getOption('updated_profile') == true) {
                $this->redirect(Yii::app()->session['urlReferrer'], true);
            } else {
                $this->redirect(absUrl('/user/profile/edit'), true);
            }
        } else {
            Yii::app()->user->setFlash('error', "Something went wrong, maybe you should clear you browser cache.");
            $this->redirect(Yii::app()->session['urlReferrer'], true);
        }
    }

    private function _linkProvider($halogin = array(), $token = NULL) {
        $haLogin = new HaLogin();
        $haLogin->login_provider_identifier = $halogin['login_provider_identifier'];
        $haLogin->login_provider = $halogin['login_provider'];
        $haLogin->user_id = $halogin['user_id'];
        $haLogin->token = (is_array($halogin['token'])) ? json_encode($halogin['token']) : $halogin['token'];
        $haLogin->token_session = Yii::app()->getModule('hybridauth')->getHybridAuth()->getSessionData();
        $haLogin->save();
    }    
}
