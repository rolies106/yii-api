<?php
class UsersController extends Controller
{
    public $layout = 'json';

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'expression' => array($this, 'apiVerify'),
                'verbs' => array('POST')
            ),
            array('deny',
                'deniedCallback' => array($this, 'apiDenied'),
            )
        );
    }

    public function actionMe()
    {
        $oauth = YiiOAuth2::instance();
        $token = Yii::app()->request->getPost('token', null);
        $tokenDetail = $oauth->getTokenRecord($token, YiiOAuth2::TOKEN_TYPE_ACCESS_TOKEN);
        $profile = array();

        if (!empty($tokenDetail)) {
            $user = User::model()->findByPk($tokenDetail['user_id']);

            if (!empty($user)) {
                $profile = array(
                    'id' => $user->id,
                    'username' => $user->username,
                    'first_name' => $user->profile->first_name,
                    'last_name' => $user->profile->last_name,
                    'email' => $user->email,
                    'join_date' => $user->create_at,
                );
            }
        }
        
        echo json_encode($profile);
    }
}