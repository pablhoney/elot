<?php
class WebUser extends CWebUser {
  private $_model;
  public $userTypes;
 
  function getEmail()
  {
        $user = $this->loadUser(Yii::app()->user->id);
	return $user->email;
  }
  
  function getUsername()
  {
        $user = $this->loadUser(Yii::app()->user->id);
	return $user->username;
  }
  
  function getPayInfo()
  {
        $userPayInfo = UserPaymentInfo::model()->find('t.user_id ='.Yii::app()->user->id);
        return $userPayInfo;
  }
  
  function getWalletValue(){
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->available_balance_amount;
  }

  function getAvatar()
  {
	$user = $this->loadUser(Yii::app()->user->id);
	if($user!==null){
            $img = $user->profile->img;
            if($img){
                return $user->profile->img;
            }
        }
	return "";
  }
  
  function isActive(){
      if(Yii::app()->user->id){
            $user = $this->loadUser(Yii::app()->user->id);
            if($user!==null)
                    return $user->is_active;
            return false;
      }
  }
  
  function isEmailConfirmed(){
      if(Yii::app()->user->id){
            $user = $this->loadUser(Yii::app()->user->id);
            if($user!==null)
                    return $user->is_email_confirmed;
            return false;
      }
  }
  
  function getUserModel(){
      if(Yii::app()->user->id){
            $user = $this->loadUser(Yii::app()->user->id);
            if($user!==null)
                    return $user;
            return false;
      }
  }
  
  function getAvatarUrl($dimension = 'smallThumb')
  {
      
        // old code:
        /*
                if($user->ext_source == Yii::app()->params['authExtSource']['Google']){
                    require_once(dirname(__FILE__).'/../extensions/hoauth/models/UserOAuth.php');
                    $config = UserOAuth::getConfig();
                    $gUrl = "https://www.googleapis.com/plus/v1/people/".$user->ext_id."?fields=image&key=".$config['providers']['Google']['keys']['api-key'];
                    $output = Yii::app()->curl->setOption(CURLOPT_HEADER,false)->get($gUrl);
                    $res = CJSON::decode($output);
                    $img = $res['image']['url'];
                }
                return CHtml::image($img, "User Avatar", array("class"=>"img-thumbnail"));
        */
	$user = $this->loadUser(Yii::app()->user->id);
        $url = Users::model()->getImageTag($user,$dimension);
        return $url;
  }
  
  function getIsAdmin()
  {
	$user = $this->loadUser(Yii::app()->user->id);
	if($user!==null)
		return ($user->user_type_id==$this->userTypes['admin']);
	return false;
  }
  
  function isAdmin()
  {
	$user = $this->loadUser(Yii::app()->user->id);
	if($user!==null)
		return ($user->user_type_id==$this->userTypes['admin']);
	return false;
  }
  
  public function login($identity,$duration=0)
  {
      if($identity->extSource==0){
          $res=parent::login($identity,$duration);
      } else {
          $authRes=$identity->authenticate();
          $res=parent::login($identity,$duration);
      }
      
      if($res){
          //track user login
          $user=$this->loadUser(Yii::app()->user->id);
          $user->last_login_ip=CHttpRequest::getUserHostAddress();
          $user->last_logged_in_time=new CDbExpression('NOW()');
          $user->save(true,null,false);
      }
      return $res;
  }
  public function logout($destroySession= true)
  {
        parent::logout();
  }

  public function loadUser($id=null)
  {
        if($this->_model===null)
        {
            if($id!==null)
                $this->_model=Users::model()->findByPk($id);
        }
        return $this->_model;
  }

  function isGuest()
  {
	if(Yii::app()->user->id)
		return !$this->isActive();
	return true;
  }
  
  function getRemainingGiftCredit(){
      $cond = new CDbCriteria();
      $cond->addCondition("t.user_id = ".Yii::app()->user->id);
      $cond->addCondition("t.modified > ".new CDbExpression("DATE_SUB(NOW(), INTERVAL 1 MONTH)"));
      $cond->addCondition("t.transaction_type = ".Yii::app()->params['userTransactionConst']['giftCreditTo']);
      $cond->select = "SUM(t.value) as sumVal";
      $sumUserGift = UserTransactions::model()->find($cond);
      $remCredit = Yii::app()->params['creditConstant']['maxMonthlyGiftCredit'] + $sumUserGift['sumVal']; // PIU' perchè il numero viene negativo, quindi va sommato
      return $remCredit;
  }
}
?>