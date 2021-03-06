<?php
class LotteryPaymentRequest extends PActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lottery_payment_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('lottery_id, from_user_id', 'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, lottery_id, from_user_id, sent_date, is_completed, complete_date, complete_by, complete_ref', 'safe', 'on'=>'search'),
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
                    'user' => array(self::BELONGS_TO, 'Users', 'from_user_id'),
                    'completeUser' => array(self::BELONGS_TO, 'Users', 'complete_by'),
                    'lottery' => array(self::BELONGS_TO, 'Auctions', 'lottery_id'),
		);
	}
        
        public function attributeLabels()
	{
		return array(
			'id' => Yii::t('wonlot','ID'),
			'lottery_id' => Yii::t('wonlot','ID Asta'),
			'from_user_id' => Yii::t('wonlot','Utente'),
			'sent_date' => Yii::t('wonlot','Data'),
			'is_completed' => Yii::t('wonlot','E\' pagata?'),
			'complete_date' => Yii::t('wonlot','Data pagamento'),
			'complete_by' => Yii::t('wonlot','Pagata da'),
			'complete_ref' => Yii::t('wonlot','Rif. Pagamento'),
			'prize_img' => Yii::t('wonlot','Premio'),
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
		$criteria->compare('lottery_id',$this->lottery_id,true);
		$criteria->compare('from_user_id',$this->from_user_id,true);
		$criteria->compare('sent_date',$this->sent_date,true);
		$criteria->compare('is_completed',$this->is_completed,true);
		$criteria->compare('complete_date',$this->complete_date,true);
		$criteria->compare('complete_by',$this->complete_by,true);
		$criteria->compare('complete_ref',$this->complete_ref,true);

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
