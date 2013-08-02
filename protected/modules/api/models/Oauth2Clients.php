<?php

/**
 * This is the model class for table "{{oauth2_clients}}".
 *
 * The followings are the available columns in table '{{oauth2_clients}}':
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 * @property string $app_owner_user_id
 * @property string $app_title
 * @property string $app_desc
 * @property string $status
 * @property string $created_at
 */
class Oauth2Clients extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Oauth2Clients the static model class
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
		return '{{oauth2_clients}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, client_secret, redirect_uri, app_owner_user_id, created_at', 'required'),
			array('client_id, client_secret', 'length', 'max'=>20),
			array('redirect_uri', 'length', 'max'=>200),
			array('app_owner_user_id', 'length', 'max'=>10),
			array('app_title', 'length', 'max'=>255),
			array('status', 'length', 'max'=>1),
			array('app_desc', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('client_id, client_secret, redirect_uri, app_owner_user_id, app_title, app_desc, status, created_at', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'client_id' => 'Client',
			'client_secret' => 'Client Secret',
			'redirect_uri' => 'Redirect Uri',
			'app_owner_user_id' => 'App Owner User',
			'app_title' => 'App Title',
			'app_desc' => 'App Desc',
			'status' => 'Status',
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

		$criteria->compare('client_id',$this->client_id,true);
		$criteria->compare('client_secret',$this->client_secret,true);
		$criteria->compare('redirect_uri',$this->redirect_uri,true);
		$criteria->compare('app_owner_user_id',$this->app_owner_user_id,true);
		$criteria->compare('app_title',$this->app_title,true);
		$criteria->compare('app_desc',$this->app_desc,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}