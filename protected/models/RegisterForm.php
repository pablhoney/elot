<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
	public $email;
	public $password;
	public $confirmEmail;
	public $confirmPassword;
	public $username;
	public $terms;
	public $persdatamng;
	public $thirdpartdatamng;
	public $newsletterAccept;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email, confirmEmail, password, confirmPassword , username, terms', 'required'),
			array('email', 'email'),
			array('confirmEmail', 'email'),
                        // flags need to be a boolean
			array('terms, persdatamng', 'boolean'),
			// Confirmation fields need to be the same as originals
                        array('confirmPassword', 'compare', 'compareAttribute'=>'password'),
                        array('confirmEmail', 'compare', 'compareAttribute'=>'email'),
                        array('username', 'unique'),
		);
	}
        
        /**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>Yii::t("wonlot","Email"),
			'confirmEmail'=>Yii::t("wonlot","Conferma Email"),
			'password'=>Yii::t("wonlot","Password"),
			'confirmPassword'=>Yii::t("wonlot","Conferma Password"),
			'username'=>Yii::t("wonlot","Nome utente"),
			'is_agree_terms_conditions'=>Yii::t("wonlot",""),
			'is_agree_personaldata_management'=>Yii::t("wonlot",""),
//			''=>Yii::t("wonlot",""),
//			'username'=>'Username',
		);
	}
        
        public function unique($attribute)
        {
            $user = Users::model()->find('t.username = "'.$this->$attribute.'"');
            if($user){
              $this->addError($attribute, 'Username già usato!');
            }
        }
        
        /**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function register()
	{
            $model=new Users;
            $model->email=$this->email;
            $model->password=sha1(Yii::app()->params['hashString'].$this->password);
            $model->username=$this->username;
            $model->user_type_id=Yii::app()->user->userTypes['user'];
            $model->is_agree_terms_conditions=$this->terms;
            $model->is_agree_personaldata_management=$this->persdatamng;
            $model->is_agree_3partdata_management=$this->thirdpartdatamng;
            $model->newsletter_terms=$this->newsletterAccept;
            $model->is_active=0;
            $model->is_email_confirmed=0; //ATTENTION: for PROD
//            $model->is_email_confirmed=1; //ATTENTION: for DEV
            $model->signup_ip=CHttpRequest::getUserHostAddress();
            $model->dns=CHttpRequest::getUserHost();
            $dbTransaction=$model->dbConnection->beginTransaction();
            
            if($model->save()){
                $profile=new UserProfiles;
                $profile->user_id=$model->id;
                if($profile->save()){
                    $dbTransaction->commit();
                    // TODO: add email activation send
                    return $model;
                } else {
                    $dbTransaction->rollback();
                    $model->addError('email', $profile->errors);
                }
            } else {
                $dbTransaction->rollback();
                $model->addError('email', $model->errors);
                return $model;
            }
	}
}
