<?php

/**
 * This is the model class for table "user_special_offers".
 *
 * The followings are the available columns in table 'user_special_offers':
 * @property string $id
 * @property string $user_id
 * @property string $offer_on
 * @property string $offer_value
 * @property string $comment
 * @property string $start_date
 * @property string $end_date
 * @property integer $times_remaining
 * @property string $created
 * @property string $modified
 * @property integer $last_modified_by
 */
class UserSpecialOffers extends PActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_special_offers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, offer_on, offer_value', 'required'),
			array('times_remaining, last_modified_by', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('offer_on', 'length', 'max'=>25),
			array('offer_value', 'length', 'max'=>15),
			array('comment', 'length', 'max'=>45),
			array('start_date, end_date, created, modified', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, offer_on, offer_value, comment, start_date, end_date, times_remaining, created, modified, last_modified_by', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'user_id' => 'User',
			'offer_on' => 'Offer On',
			'offer_value' => 'Offer Value',
			'comment' => 'Comment',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'times_remaining' => 'Times Remaining',
			'created' => 'Created',
			'modified' => 'Modified',
			'last_modified_by' => 'Last Modified By',
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
		$criteria->compare('offer_on',$this->offer_on,true);
		$criteria->compare('offer_value',$this->offer_value,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('times_remaining',$this->times_remaining);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);
		$criteria->compare('last_modified_by',$this->last_modified_by);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your PActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserSpecialOffers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}