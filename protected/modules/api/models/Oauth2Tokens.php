<?php

/**
 * This is the model class for table "{{oauth2_tokens}}".
 *
 * The followings are the available columns in table '{{oauth2_tokens}}':
 * @property string $oauth_token
 * @property string $token_type
 * @property string $client_id
 * @property string $user_id
 * @property integer $expires
 * @property string $redirect_uri
 * @property string $scope
 * @property string $refresh_token
 * @property string $created_at
 */
class Oauth2Tokens extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Oauth2Tokens the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{oauth2_tokens}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('oauth_token, client_id, user_id, expires, created_at', 'required'),
            array('expires', 'numerical', 'integerOnly'=>true),
            array('oauth_token', 'length', 'max'=>40),
            array('token_type', 'length', 'max'=>7),
            array('client_id', 'length', 'max'=>20),
            array('user_id', 'length', 'max'=>11),
            array('redirect_uri, scope', 'length', 'max'=>200),
            array('refresh_token', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('oauth_token, token_type, client_id, user_id, expires, redirect_uri, scope, refresh_token, created_at', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'client' => array(self::BELONGS_TO, 'Oauth2Clients', 'client_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'oauth_token' => 'Oauth Token',
            'token_type' => 'Token Type',
            'client_id' => 'Client',
            'user_id' => 'User',
            'expires' => 'Expires',
            'redirect_uri' => 'Redirect Uri',
            'scope' => 'Scope',
            'refresh_token' => 'Refresh Token',
            'created_at' => 'Created At',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('oauth_token',$this->oauth_token,true);
        $criteria->compare('token_type',$this->token_type,true);
        $criteria->compare('client_id',$this->client_id,true);
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('expires',$this->expires);
        $criteria->compare('redirect_uri',$this->redirect_uri,true);
        $criteria->compare('scope',$this->scope,true);
        $criteria->compare('refresh_token',$this->refresh_token,true);
        $criteria->compare('created_at',$this->created_at,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Check is user already authorize application
     *
     * @param int $client_id Application ID
     * @param int $user_id User ID
     * @param bool $bool Is return as bool
     * @return mixed
     */
    public function isAuthorize($client_id, $user_id = null, $bool = true)
    {
        $user = (!empty($user_id)) ? $user_id : Yii::app()->user->id;

        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id = :uid AND client_id = :client AND token_type = "access"';
        $criteria->params = array(':uid' => $user, ':client' => $client_id);

        $result = self::model()->find($criteria);

        if (!empty($result)) {

            if ($result->expires > time()) {
                if ($bool) {    
                    return true;
                } else {
                    return $result;
                }                
            } else {
                $result->delete();
            }

            return false;
        }

        return false;
    }
}