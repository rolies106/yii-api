<?php

class AuthController extends Controller
{
    public function actionIndex() {}

    public function actionAccess_token()
    {
        $oauth = YiiOAuth2::instance();
        $code = Yii::app()->request->getPost('code', null);
        $codeDetail = $oauth->getTokenRecord($code, YiiOAuth2::TOKEN_TYPE_CODE);
        $oauth->setVariable("user_id", $codeDetail['user_id']);
        $oauth->setVariable("redirect_uri", $codeDetail['redirect_uri']);
        echo $oauth->grantAccessToken();
    }

    public function actionAuthorize()
    {
        $oauth = YiiOAuth2::instance();
        $model = new LoginForm();
        $auth_params = $oauth->getAuthorizeParams();

        $app = $oauth->getClients($auth_params['client_id']);

        if (!Yii::app()->user->isGuest) {
            $user_id = Yii::app()->user->id;
            $oauth->setVariable("user_id", $user_id);
        }

        if (Yii::app()->request->isPostRequest) {
            if (isset($_POST['LoginForm']) && !isset($_POST['authorize']))
            {
                $model->attributes = $_POST['LoginForm'];
                $_POST['response_type'] = Yii::app()->request->getQuery('response_type', null);
                $_POST = array_merge($_POST, $app);

                if($model->validate())
                {
                    $user_id = Yii::app()->user->id;
                }
            } else if (isset($_POST['authorize'])) {
                $oauth->finishClientAuthorization($_POST['authorize'], $_POST);
            }
        }
        
        // render the authorize page
        $this->render('authorize', array('model'=>$model, 'app'=>$app, 'auth_params'=>$auth_params));
    }
}