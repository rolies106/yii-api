<?php

class SiteController extends Controller
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
                'actions'=>array('*')
            )
        );
    }

    public function actionIndex()
    {
        
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $this->layout = 'json';

        $error['code'] = Yii::app()->errorHandler->error['code'];
        $error['message'] = Yii::app()->errorHandler->error['message'];#'Endpoint is not recognize';

        echo json_encode($error);
    }
}