<?php
/**
 * HOAuth provides widget with buttons for login with social networs 
 * that enabled in HybridAuth config
 * 
 * @uses CWidget
 * @version 1.2.4
 * @copyright Copyright &copy; 2013 Sviatoslav Danylenko
 * @author Sviatoslav Danylenko <dev@udf.su> 
 * @license MIT ({@link http://opensource.org/licenses/MIT})
 * @link https://github.com/SleepWalker/hoauth
 */

/**
 * NOTE: If you want to change the order of button it is better to change this order in HybridAuth config.php file
 */
class HOAuth extends CWidget
{
	/**
	 * @var string $route id of module and controller (eg. module/controller) for wich to generate oauth urls
	 */
	public $route = false;

	/**
	 * @var boolean $onlyIcons the flag that displays social buttons as icons
	 */
	public $onlyIcons = false;

	/**
	 * @var integer $popupWidth the width of the popup window
	 */
	public $popupWidth = 480;

	/**
	 * @var integer $popupHeight the height of the popup window
	 */
	public $popupHeight = 680;

	public function init()
	{
		if(!$this->route)
			$this->route = $this->controller->module ? $this->controller->module->id . '/' . $this->controller->id : $this->controller->id;
		
		require_once(dirname(__FILE__).'/../models/UserOAuth.php');
		require_once(dirname(__FILE__).'/../HOAuthAction.php');
		$this->registerScripts();
	}

	public function run()
	{
		$config = UserOAuth::getConfig();
		echo CHtml::openTag('div', array(
			'id' => 'hoauthWidget' . $this->id,
			'class' => 'hoauthWidget',
			));

		foreach($config['providers'] as $provider => $settings){
			if($settings['enabled']){
				$this->render('link', array(
					'provider' => $provider,
				));
                        }
                }
		echo CHtml::closeTag('div');
	}

	protected function registerScripts()
	{
		$assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets',false,-1,YII_DEBUG);
    $cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery'); 
    $cs->registerCssFile($assetsUrl.'/css/zocial.css');
    ob_start();
		?>
		$(function() {
			$('.hoauthWidget a').click(function() {
                            alert(this.className);
                            alert(this.className.indexOf("facebook"));
                            if(this.className.indexOf("facebook") >= 0){
                                alert("Facebook");
                                /*FB.api('/me', function(response) {
                                    alert(JSON.stringify(response));
                                }*/
                                FB.login(function(response) {
                                    alert("LOGIN!");
                                });
                            }
                                
				
				return false;
			});
		});
                /*(function() {
                    var po = document.createElement('script');
                    po.type = 'text/javascript'; po.async = true;
                    po.src = 'https://apis.google.com/js/client:plusone.js?onload=render';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(po, s);
                  })();
                (function(d, s, id){
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/en_US/all.js";
                    fjs.parentNode.insertBefore(js, fjs);
                  }(document, 'script', 'facebook-jssdk'));
                  function signinCallback(authResult) {
                        if (authResult['status']['signed_in']) {
                            gapi.client.load('plus','v1', function(){
                                if (authResult['access_token']) {
                                    authStatus = true;
                                } else if (authResult['error']) {
                                    alert("Google login error");
                                }
                            });
                        } else {
                          console.log('Sign-in state: ' + authResult['error']);
                          authStatus = false;
                        }
                  }
                  var gpdefaults = {
                        clientid: "<?php echo $config['providers']['Google']['keys']['id'];?>",
                        callback: signinCallback,
                        cookiepolicy: 'single_host_origin',
                        requestvisibleactions: 'http://schemas.google.com/AddActivity',
                        scope: 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email',
                  };
                  var gpInviteBtnOptions = {
                            clientid: "<?php echo $config['providers']['Google']['keys']['id'];?>",
                            cookiepolicy: 'single_host_origin',
                            prefilltext: 'Create your Google+ Page too!',
                            calltoactiondeeplinkid: '/pages/create'
                  };
                  window.fbAsyncInit = function() {
                    FB.init({
                      appId      : "<?php echo $config['providers']['Facebook']['keys']['id'];?>",
                      status     : true,
                      xfbml      : true,
                      display    : "popup"
                    });
                  };
                  var fbScope={scope: 'email,user_birthday'};*/
                  /*function render() {

                    // Additional params
                    var additionalParams = {
                      'theme' : 'dark'
                    };

                    gapi.signin.render('googleLoginButton', additionalParams);
                  }*/
<?php
    $cs->registerScript(__CLASS__, ob_get_clean());
	}
}
