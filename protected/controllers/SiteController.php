<?php

class SiteController extends Controller
{
        public $layout='//layouts/column1';
        
        /**
	 * Declares class-based actions.
	 */
	public function actions()
	{
            return array_merge(parent::actions(),
		array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
                        'oauth' => array(
                            // the list of additional properties of this action is below
                            'class'=>'ext.hoauth.HOAuthAction',
                            // Yii alias for your user's model, or simply class name, when it already on yii's import path
                            // default value of this property is: User
                            'model' => 'Users', 
                            // map model attributes to attributes of user's social profile
                            // model attribute => profile attribute
                            // the list of avaible attributes is below
                            'attributes' => array(
                              'email' => 'email',
                              'ext_id' => 'identifier',
                              'password' => 'identifier',
                              'username' => 'displayName',
                              'profile->first_name' => 'firstName',
                              'profile->last_name' => 'lastName',
                              'profile->gender' => 'genderShort',
                              'profile->birthday' => 'birthDate',
                              'profile->img' => 'photoURL',
                              // you can also specify additional values, 
                              // that will be applied to your model (eg. account activation status)
                              /*'acc_status' => 1,*/
                            ),
                        ),
                        'oauthshare' => array(
                            // the list of additional properties of this action is below
                            'class'=>'ext.hoauth.HOAuthShareAction',
                            // Yii alias for your user's model, or simply class name, when it already on yii's import path
                            // default value of this property is: User
                            'model' => 'Users', 
                        ),
		)
            );        
	}

        public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','error','login','register','contact','socialShare'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('logout',''),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','adminCron'),
				'users'=>array('@'),
                                'expression' => 'Yii::app()->user->isAdmin()',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
                $this->layout='//layouts/index';
		$this->render('index');
	}
        
	public function actionAcceptPolicy()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
                $this->layout='//layouts/index';
		$this->render('acceptPolicy');
	}
	
        public function actionAdmin()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
                $this->layout='//layouts/index';
		$this->render('admin');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
            // collect user input data
            if (isset($_POST['LoginForm']))
            {   
                $model = new LoginForm;
                $model->attributes = $_POST['LoginForm'];
                // validate user input and redirect to the previous page if valid
                if ($model->validate() && $model->login())
                {
                    $data = array(
                        'authenticated' => true,
                        'redirectUrl' => $_POST['LoginForm']['originUrl'],
                        'afterLogin' => true,
                    );
                } else {
                    $data = array(
                        'authenticated' => false,
                        'redirectUrl' => Yii::app()->user->returnUrl,
                        'afterLogin' => true,
                        'showLogin' => true,
                    );
                }
                $data['model'] = $model;
            }
            $this->renderPartial('login', $data, false, true);
	}
        
	/**
	 * Displays the register page
	 */
	public function actionRegister()
	{
                if(!Yii::app()->user->isGuest()){
                    $this->redirect(Yii::app()->homeUrl);
                }
                if($_POST['RegisterForm']){
                    $model=new RegisterForm;
                    $model->attributes = $_POST['RegisterForm'];
                } else {
                    $model=new RegisterForm;
                }

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='register-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['RegisterForm']))
		{
                    
                    // check if already registered
                    $already = Users::model()->find('email = "'.$_POST['RegisterForm']['email'].'"');
                    if($already){
                        $model->addError('email', 'Email already registered. Try login.');
                    } else {
                        $model->attributes=$_POST['RegisterForm'];
                        if(!$model->terms){
                            $model->addError('terms', 'Devi accettare i Termini e Condizioni');
                        } else {
                            // validate user input and redirect to the previous page if valid
                            if($model->validate()){
                                $newUser = $model->register();
                                if(!$newUser->getErrors()){
                                    EmailManager::sendConfirmEmail($newUser);
                                    $this->redirect(Yii::app()->homeUrl);
                                } else {
                                    $model->errors = $newUser->getErrors();
                                }
                            }
                        }
                    }
		}
		// display the login form
		$this->render('register',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
        
	public function actionSocialShare()
	{
		$facebook = Yii::app()->hoAuth->getAdapter('Facebook');
                $facebook->isUserConnected();
                $user = $facebook->getUserProfile();
                echo $user->email;
                echo $user->photoURL;
                $facebook->api()->api('/me/friends', "post", array(message => "Hi there")); // post 
	}
        
        public function hoauthAfterLogin($user,$newUser,$oAuth) {
            // check if new User
            if(!$newUser){
                $socialUser = SocialUser::model()->find('t.user_id='.$user->id.' AND t.login_type='.Yii::app()->params['authExtSource'][$oAuth->provider]);
                if($socialUser){
                    if($socialUser->user_id != Yii::app()->user->id){
                        Yii::app()->user->logout();
                        $identity=new UserIdentity($socialUser->user->username,$socialUser->user->password,Yii::app()->params['authExtSource']['site']);
                        $identity->authenticate();
                        $duration=3600*24; // 1 day
                        Yii::app()->user->login($identity,$duration);
                    } else {
                        if($socialUser->ext_user_id != $oAuth->identifier){ // diff ext user id: maybe new account
                            $socialUser->ext_user_id = $oAuth->identifier;
                            $socialUser->linked_on = new CDbExpression('NOW()');
                            $socialUser->save();
                        }
                    }
                } else {
                    $newSocialUser = new SocialUser();
                    $newSocialUser->user_id = $user->id;
                    $newSocialUser->login_type = Yii::app()->params['authExtSource'][$oAuth->provider];
                    $newSocialUser->ext_user_id = $oAuth->identifier;
                    $newSocialUser->linked_on = new CDbExpression('NOW()');
                    $newSocialUser->save();
                }
                Users::model()->getGiftTicketsAfterRegister($oAuth->identifier);
            } else {
                $newSocialUser = new SocialUser();
                $newSocialUser->user_id = $user->id;
                $newSocialUser->login_type = Yii::app()->params['authExtSource'][$oAuth->provider];
                $newSocialUser->ext_user_id = $oAuth->identifier;
                $newSocialUser->linked_on = new CDbExpression('NOW()');
                $newSocialUser->save();
//                $ticketRes = Users::model()->getGiftTicketsAfterRegister(Yii::app()->params['authExtSource'][$oAuth->provider]);
                Users::model()->getGiftTicketsAfterRegister($oAuth->identifier);
            }
        }
        
        public function actionAdminCron(){
            Yii::log("CRON START!", "warning");
            // ADD TO CRONTAB: php /path/to/cron.php Cron
            $busy = file_exists(Yii::app()->basePath."/cron-lottery.lock");
            if(!$busy){
                Yii::log("CRON OK!", "warning");
                $file=Yii::app()->basePath."/cron-lottery.lock";
                $oFile=fopen($file,"w");
                fwrite($oFile,"DO");
                fclose($oFile);
                try {
                    $errors = array('open'=>array(),'close'=>array(), 'extract'=>array(),'void'=>array());
                    Auctions::model()->checkToOpen($errors);
                    Yii::log("CRON 1", "warning");
                    Auctions::model()->checkToClose($errors);
                    Yii::log("CRON 2", "warning");
                    Auctions::model()->checkToExtract($errors);
                    Yii::log("CRON 3", "warning");
                    Auctions::model()->checkToVoid($errors);
                    Yii::log("CRON Fine", "warning");
                    if(count($errors['open'])+
                       count($errors['close'])+
                       count($errors['extract'])+
                       count($errors['void']) > 0){
                       $emailRes=EmailManager::sendCronAdminEmail($errors);
                    }
                } catch (Exception $exc) {
                    Yii::log("CRON error:".$exc->getTraceAsString(),'error');
                }
                unlink($file);
            } else {
                Yii::log("CRON Busy -> SKIP ", "warning");
            }
            Yii::log("CRON END!", "warning");
        }
}