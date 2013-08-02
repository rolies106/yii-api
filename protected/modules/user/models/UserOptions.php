<?php

/**
 * This is the model class for table "{{user_options}}".
 *
 * The followings are the available columns in table '{{user_options}}':
 * @property integer $user_id
 * @property string $option_name
 * @property string $option_value
 */
class UserOptions extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return UserOptions the static model class
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
        return '{{user_options}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, option_name, option_value', 'required'),
            array('user_id', 'numerical', 'integerOnly'=>true),
            array('option_name, option_value', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('user_id, option_name, option_value', 'safe', 'on'=>'search'),
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
            'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'user_id' => 'User',
            'option_name' => 'Option Name',
            'option_value' => 'Option Value',
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

        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('option_name',$this->option_name,true);
        $criteria->compare('option_value',$this->option_value,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /* Set user option */
    public function setOption($option_name, $option_value, $uid = NULL)
    {
        $user = (!empty($uid)) ? $uid : app()->user->id;

        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id = :user AND option_name = :opname';
        $criteria->params = array(':user' => $user, ':opname' => $option_name);

        $existOption = self::model()->find($criteria);

        if (empty($existOption)) {
            $option = new UserOptions;
            $option->user_id = $user;
            $option->option_name = $option_name;
            $option->option_value = $option_value;
            $saved = $option->save();

            return $saved;
        } else {
            $existOption->option_value = $option_value;
            $saved = $existOption->save();

            return $saved;
        }
    }

    /* Get user option */
    public function getOption($option_name, $uid = NULL)
    {
        $user = (!empty($uid)) ? $uid : app()->user->id;

        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id = :user AND option_name = :opname';
        $criteria->params = array(':user' => $user, ':opname' => $option_name);

        $existOption = self::model()->find($criteria);

        if (!empty($existOption)) {
            return $existOption->option_value;
        } else {
            return $existOption;
        }
    }   
}