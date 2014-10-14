<?php

/**
 * BuyForm class.
 * BuyForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class GiftForm extends CFormModel
{
	public $email;
	public $offerId;
	public $lotId;
	public $giftToUserId;
	public $giftToUsername;
	public $giftToEmail;
	public $provider;
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
                        // email has to be a valid email address
			array('email', 'email'),
			// verifyCode needs to be entered correctly
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'offerId' => Yii::t('wonlot','Offerta speciale')
		);
	}
}