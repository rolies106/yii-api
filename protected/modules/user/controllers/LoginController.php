<?php

class LoginController extends Controller
{
    public $defaultAction = 'login';

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (Yii::app()->user->isGuest) {
            $model=new UserLogin;

            // collect user input data
            if(isset($_POST['UserLogin']))
            {
                $model->attributes=$_POST['UserLogin'];
                // validate user input and redirect to previous page if valid
                if($model->validate()) {
                    $this->lastViset();

                    # Handle ajax request
                    if (Yii::app()->request->isAjaxRequest) {
                        $return['redirect_url'] = absUrl(Yii::app()->user->returnUrl);
                        $return['message'] = 'Login Successfully';
                        $return['success'] = true;
                        echo json_encode($return);
                        die();
                    }

                    if (Yii::app()->getBaseUrl()."/index.php" === Yii::app()->user->returnUrl) {
                        $this->redirect(absUrl('/'));
                        exit();                     
                    } else {
                        $this->redirect(Yii::app()->user->returnUrl);
                        exit();                     
                    }
                } else {
                    # Handle ajax request
                    if (Yii::app()->request->isAjaxRequest) {
                        $return['redirect_url'] = '';
                        $return['message'] = CHtml::errorSummary($model);
                        $return['success'] = false;
                        echo json_encode($return);
                        die();
                    }                   
                }
            }
            // display the login form
            $this->render('/user/login',array('model'=>$model));
        } else {
            $this->redirect(absUrl(Yii::app()->controller->module->returnUrl[0]));
            exit();
        }
    }
    
    private function lastViset() {
        $lastVisit = User::model()->notsafe()->findByPk(Yii::app()->user->id);
        $lastVisit->lastvisit = time();
        $lastVisit->save();
    }

}