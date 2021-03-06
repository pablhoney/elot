<?php
class UserWithdraw extends PActiveRecord
{
        public $creditOption;
        public $creditValue;
    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_withdraw';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('user_id, value, status', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('user_id, value, status, paid_by, paid_on, paid_ref', 'safe', 'on'=>'search'),
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
                    'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
                    'lastModUser' => array(self::BELONGS_TO, 'Users', 'last_modified_by'),
                    'paidUser' => array(self::BELONGS_TO, 'Users', 'paid_by'),
		);
	}
        
        public function attributeLabels()
	{
		return array(
			'id' => Yii::t('wonlot','ID'),
			'user_id' => Yii::t('wonlot','User'),
			'value' => Yii::t('wonlot','Valore'),
			'status' => Yii::t('wonlot','Stato'),
			'paid_by' => Yii::t('wonlot','Pagato da'),
			'paid_on' => Yii::t('wonlot','Pagato il'),
			'last_modified_by' => Yii::t('wonlot','Ultima modifica di'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('paid_by',$this->paid_by,true);
		$criteria->compare('paid_on',$this->paid_on,true);
		$criteria->compare('last_modified_by',$this->last_modified_by,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your PActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserProfiles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
}
?>
