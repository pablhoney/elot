<?php
require_once Yii::getPathOfAlias('ext.PHPStats') .  DIRECTORY_SEPARATOR . 'PHPStats.phar';
class AuctionsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/basecolumn';
        
        public $ticketTotals;
        public $actualWeight;
        public $giftTicketTotals;
        
        public $lotErrors = array();
        
        private $randValues = array(
            'k' => 1,
            'lambda'=>12
        );


        /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','category','delete','getPartialArray'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','userIndex','upload','buyTicket','setDefault',
                                                 'deleteImg','giftTicket','setFavorite','unsetFavorite',
                                                 'getPartialView'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('update','void','clone','void'),
                                'expression' => array('AuctionsController','allowOnlyOwner'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','cronLottery','random'),
                                'expression' => array('AuctionsController','isAdmin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
                                'deniedCallback' => array($this, 'redirectToHome'), 
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
        public function redirectToHome(){
            $this->redirect(Yii::app()->getBaseUrl(true));
        }
        
	public function actionView($id)
	{
//                $this->layout="//layouts/allpage";
                $this->layout='//layouts/column1';
                if(!Yii::app()->user->isGuest){
//                    $this->ticketTotals=Tickets::model()->getMyTicketsNumberByLottery($id);
                    $this->ticketTotals=Tickets::model()->getMyTicketsByLottery($id);
                    $this->actualWeight = Tickets::model()->getMyTotalForLottery($id);
                    $this->giftTicketTotals=Tickets::model()->getMyGiftTicketsByLottery($id);
                }
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionGetPartialView()
	{
                $view = "_".$_POST['view']."Ajax";
                $id = $_POST['lotId'];
                if(!Yii::app()->user->isGuest){
                    $this->ticketTotals=Tickets::model()->getMyTicketsByLottery($id);
                    $this->actualWeight = Tickets::model()->getMyTotalForLottery($id);
                    $this->giftTicketTotals=Tickets::model()->getMyGiftTicketsByLottery($id);
                }
		$this->renderPartial($view,array('data'=>$this->loadModel($id)));
	}
        
        public function actionGetPartialArray(){
            $id = $_POST['lotId'];
            $winnerId = $_POST['winnerId'];
            $winnerVal = $_POST['winnerVal'];
            
            $model = $this->loadModel($id);
            if($model->winning_sum == $winnerVal && $model->winningUser->id == $winnerId){
                //return array('nochange'=>true);
                return array();
            }
            $this->renderPartial('_winningBox',array('data'=>$model));
        }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
                $paymentInfo = Yii::app()->user->payInfo;
                if(!$paymentInfo){
                    $this->redirect('/users/payInfo');
                }
                $this->sideView='createLotteyHelp';
                $model=new Auctions;
                $this->_editLottery($model);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
//                $this->layout='column2l6l4';
                $this->sideView='createLotteyHelp';
                $model = $this->loadModel($id);
                $updatableStatus = array(
                    Yii::app()->params['lotteryStatusConst']['draft'],
                    Yii::app()->params['lotteryStatusConst']['upcoming'],
                    Yii::app()->params['lotteryStatusConst']['open'],
                );
                if(in_array($model->status,$updatableStatus)){
                    $this->_editLottery($model);
                } else {
                    $this->lotErrors['update'] = Yii::t('wonlot','Asta non modificabile');
                    if(!Yii::app()->user->isGuest){
//                    $this->ticketTotals=Tickets::model()->getMyTicketsNumberByLottery($id);
                        $this->ticketTotals=Tickets::model()->getMyTicketsByLottery($id);
                        $this->actualWeight = Tickets::model()->getMyTotalForLottery($id);
                        $this->giftTicketTotals=Tickets::model()->getMyGiftTicketsByLottery($id);
                    }
                    $this->layout='//layouts/basecolumn';
                    $this->render('view',array(
                            'model'=>$model,
                    ));
                }
	}
	
        /**
	 * Clone a particular model.
	 * If Clone is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionClone($id)
	{
//                $this->layout='column2l6l4';
                $this->sideView='createLotteyHelp';
                $model = $this->loadModel($id);
                /*unset($model->id);
                unset($model->lottery_start_date);
                unset($model->lottery_close_date);
                unset($model->lottery_extract_date);
                unset($model->lottery_draw_date);
                unset($model->ticket_sold);
                unset($model->ticket_sold_value);
                unset($model->winning_id);
                unset($model->winning_sum);
                unset($model->winner_id);
                unset($model->winner_ticket_id);*/
                $newModel = new Auctions;
                //$newModel->setAttributes($model->attributes);
                $copyArray = [
                    'name','owner_id','lottery_type','is_charity','prize_desc',
                    'prize_category','prize_img','prize_conditions','prize_condition_text',
                    'prize_shipping','prize_price','ticket_value','location_id'
                ];
                foreach($copyArray as $copyAtt){
                    $newModel->{$copyAtt} = $model->{$copyAtt};
                }
                
                $newModel->cloneId = $id;
                $this->_editLottery($newModel);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
//		$this->loadModel($id)->delete();
                $lot=$this->loadModel($id);
                
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                }
	}
        
	public function actionRandom()
	{
                $random = array();
                
                $beta = new \PHPStats\ProbabilityDistribution\Weibull($this->randValues['lambda'],$this->randValues['k']);
                for($i=0;$i<1000;$i++){
                    $t0 = ceil($beta->rvs());
                    if($t0 > 100) $t0 = $t0%100;
                    $random[floor($t0/10)] += 1;
                }
                $this->render('random',array(
                    'random'=>$random,
                    'randValues'=>$this->randValues,
                ));
	}
        
	/**
	 * Void a lottery.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionVoid($id)
	{
                // check for STATUS ( == OPEN 3) and extraction_date (more than 24 hours later)
                $lot = $this->loadModel($id);
		if(!$lot->status == Yii::app()->params['lotteryStatusConst']['open']){
                    echo Yii::t('wonlot','Non puoi annullare questa Asta: non è aperta');
                    return;
                }
                //$lotDate = DateTime::createFromFormat('d/m/yy hh:mm:ss',$lot->lottery_draw_date);
                $lotDate = CDateTimeParser::parse($lot->lottery_draw_date, Yii::app()->params['toDbDateTimeFormat']);
                $lotDate = $lotDate - (60 *60 * 1);
                $now = CDateTimeParser::parse(date("d/m/Y H:m:s"), Yii::app()->params['toDbDateTimeFormat']);
                if($now > $lotDate){
                    echo Yii::t('wonlot','Non puoi annullare questa Asta: manca meno di 1 ora');
                    return;
                }
                $dbTransaction=$lot->dbConnection->beginTransaction();
                $lot->status = Yii::app()->params['lotteryStatusConst']['void'];
                if($lot->save()){
                    //repay tickets
                    $allOk=true;
                    $errors = array();
                    $usersRefound = array();
                    foreach($lot->validTickets as $vt){
                        $vt->status = Yii::app()->params['ticketStatusConst']['refunded'];
                        if($vt->save()){
                            if($vt->is_gift && $vt->gift_from_id){
                                $vt->giftFromUser->available_balance_amount += $vt->price;
                                if($vt->promotion_id){
                                    foreach($vt->giftFromUser->offers as $off){
                                        if($off->id == $vt->promotion_id){
                                            $off->times_remaining += 1;
                                            if(!$off->save()){
                                                $allOk=false;
                                                $errors[$vt->id][] = "Void lottery error: repaing special offer for ticket ".$vt->id;
                                                Yii::log("Void lottery error: repaing special offer for ticket ".$vt->id);
                                            }
                                        }
                                    }
                                }
                                if($vt->giftFromUser->save()){
                                    //$dbTransaction->commit();
                                    if(!in_array($vt->giftFromUser->id,$usersRefound)){
                                        $usersRefound[] = $vt->giftFromUser->id;
                                    }
                                    UserTransactions::model()->addVoidTicketRepay($vt->id,$vt->price,$vt->giftFromUser->id);
                                } else {
                                    //$dbTransaction->rollback();
                                    $allOk=false;
                                    Yii::log("Void lottery error: saving gift user. Ticket ".$vt->id);
                                }
                            } else {
                                $vt->user->available_balance_amount += $vt->price;
                                if($vt->promotion_id){
                                    foreach($vt->user->offers as $off){
                                        if($off->id == $vt->promotion_id){
                                            $off->times_remaining += 1;
                                            if(!$off->save()){
                                                $allOk=false;
                                                $errors[$vt->id][] = "Void lottery error: repaing special offer for ticket ".$vt->id;
                                                Yii::log("Void lottery error: repaing special offer for ticket ".$vt->id);
                                            }
                                        }
                                    }
                                }
                                if($vt->user->save()){
                                    //$dbTransaction->commit();
                                    if(!in_array($vt->user->id,$usersRefound)){
                                        $usersRefound[] = $vt->user->id;
                                    }
                                    UserTransactions::model()->addVoidTicketRepay($vt->id,$vt->price,$vt->user->id);
                                } else {
                                    //$dbTransaction->rollback();
                                    $allOk=false;
                                    $errors[$vt->id][] = "Void lottery error:  saving user. Ticket ".$vt->id;
                                    Yii::log("Void lottery error: saving user. Ticket ".$vt->id);
                                }
                            }
                        } else {
                            //$dbTransaction->rollback();
                            $allOk=false;
                            break;
                        }
                    }
                }
                if(!$allOk){
                    $dbTransaction->rollback();
                    $emailRes=EmailManager::sendCronAdminEmail($errors);
                } else {
                    $dbTransaction->commit();
                    foreach($usersRefound as $userRef){
                        Notifications::model()->sendRefoundLotteryNotify($lot,$userRef);
                    }
                }
                
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_POST['isAjax'])){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : '/auctions/userIndex');
                } else {
                    echo 1;
                }
	}

	/**
	 * Lists all models.
	 */ 
	public function actionIndex()
	{
            if($_POST['reset']){
                $_POST['SearchForm'] = null;
            } 
            if($_POST['SearchForm'] == null){
                unset(Yii::app()->session['filters']);
            }
            if($_POST['SearchForm']['Category']){
                $_POST['SearchForm']['Categories']=$_POST['SearchForm']['Category'];
            }
            if($_POST['SearchForm']){
                Yii::app()->session['filters'] = $_POST['SearchForm'];
            } else {
                $_POST['SearchForm'] = Yii::app()->session['filters'];
                if(!$_POST['SearchForm']){
                    $_POST['SearchForm']['LotStatus'] = array(1);
                }
            }
            $auctions=$this->filterAuctions(false);
            if(!Yii::app()->user->isGuest){
                $this->ticketTotals=Tickets::model()->getMyTicketsNumberAllAuctions();
            }
            /*$this->render('index',array(
                'auctions'=>$auctions['auctions'],
                'pages'=>$auctions['pages'],
                'viewType'=>'_show',
                'viewData'=>$auctions['viewData'],
            ));*/
            $this->render('index',array(
                'dataProvider'=>$auctions['dataProvider'],
                'viewType'=>'_show',
                'viewData'=>$auctions['viewData'],
            ));
	}

	/**
         * Load user Auctions
         */
        public function actionUserIndex()
	{
            if($_POST['reset']){
                $_POST['SearchForm'] = null;
            } 
            if($_POST['SearchForm'] == null){
                unset(Yii::app()->session['filters']);
            }
            if($_POST['SearchForm']['Category']){
                $_POST['SearchForm']['Categories']=$_POST['SearchForm']['Category'];
            }
            if($_POST['SearchForm']){
                Yii::app()->session['filters'] = $_POST['SearchForm'];
            } 
            $auctions=$this->filterAuctions(true);
            $this->ticketTotals=Tickets::model()->getMyTicketsNumberAllAuctions();
            $this->layout='//layouts/basecolumn';
            $this->render('userIndex',array(
                'dataProvider'=>$auctions['dataProvider'],
                'viewType'=>"_box",
                'viewData'=>$auctions['viewData'],
            ));
	}
        
        public function actionSetFavorite(){
            $lotId=$_POST['lotId'];
            $res = 0;
            if($lotId){
                $userId = Yii::app()->user->id;
                $checkFav = FavoriteLottery::model()->find('t.lottery_id='.$lotId.' AND t.user_id='.$userId);
                if($checkFav){
                    if($checkFav->active != 1){
                        $checkFav->active = 1;
                        if($checkFav->save()){
                            $res = 1;
                        } else {
                            $res = 0;
                        }
                    } else {
                        $res = 1;
                    }
                } else {
                    $newFav = new FavoriteLottery;
                    $newFav->lottery_id = $lotId;
                    $newFav->user_id = $userId;
                    $newFav->active = 1;
                    if($newFav->save()){
                        $res = 1;
                    } else {
                        $res = 0;
                    }
                }
            } else {
                $res = 0;
            }
            
            echo CJSON::encode($res);
        }
        
        public function actionUnsetFavorite(){
            $lotId=$_POST['lotId'];
            $res = 0;
            if($lotId){
                $userId = Yii::app()->user->id;
                $checkFav = FavoriteLottery::model()->find('t.lottery_id='.$lotId.' AND t.user_id='.$userId);
                if($checkFav){
                    if($checkFav->active != 0){
                        $checkFav->active = 0;
                        if($checkFav->save()){
                            $res = 1;
                        } else {
                            $res = 0;
                        }
                    }
                } else {
                    $res = 1;
                }
            } else {
                $res = 0;
            }
            
            echo CJSON::encode($res);
        }
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Auctions('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Auctions']))
			$model->attributes=$_GET['Auctions'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
        
	/**
	 * Manages all models.
	 */
	public function actionCronLottery()
	{
                $errors = array();
                $errors['open'] = Auctions::model()->checkToOpen($errors);
                $errors['close'] = Auctions::model()->checkToClose($errors);
//                $errors['extract'] = Auctions::model()->checkToExtract($errors);
                if(count($errors['open'])+
                    count($errors['close'])+
                    count($errors['extract']) > 0){
                    $emailRes=EmailManager::sendCronAdminEmail($errors);
                    $errors['count'] = count($errors['open'])+count($errors['close'])+count($errors['extract']);
                }
		
		$this->render('/site/admin',array(
			'errors'=>$errors,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Auctions the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Auctions::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
        
        public function isDefaultImage($imgName,$imgDb){
            $fileName=explode("/", $imgName);
            $fileName=$fileName[count($fileName)-1];
            if($imgName === $imgDb){
                return TRUE;
            } else {
                return FALSE;
            }
        }
        
        /**
	 * AJAX SECTIONS.
	 * actionBuyTicket -> buy a ticket for lottery
         * actionSetDefault ->set default image to existing lottery
	 */
        public function setGift($ticket,$formPost){
            $res = false;
            $msg = "";
            $toUser = "";
            if(empty($formPost['provider'])){
                $msg = Yii::t('wonlot','Dati mancanti');
            } else {
                switch ($formPost['provider']) {
                    case "Email":
                        $emailValidator = new CEmailValidator();
                        $emailValid = $emailValidator->validateValue($formPost['giftToEmail']);
                        if(!empty($formPost['giftToEmail']) && $emailValid) {
                            $checkExistUserCrit = new CDbCriteria();
                            $checkExistUserCrit->addCondition('t.email = "'.$formPost['giftToEmail'].'"');
                            $checkExistUserCrit->addCondition('t.is_active = 1');
                            $user = Users::model()->find($checkExistUserCrit);
                            if($user->email == Yii::app()->user->email){
                                $msg = Yii::t('wonlot','Non puoi regalare a te stesso');
                            } else {
                                if($user){
                                    $ticket->user_id = $user->id;
                                    $sendNotify = true;
                                    $toUser = $user->username;
                                } else {
                                    $ticket->gift_provider = 'email';
                                    $ticket->gift_ext_user = $formPost['giftToEmail'];
                                    $ticket->gift_ext_username = $formPost['giftToEmail'];
                                    $toUser = $ticket->gift_ext_username;
                                }
                                $ticket->is_gift = 1;
                                $ticket->is_sent = 0;
                                $ticket->gift_from_id = Yii::app()->user->id;
                                if($ticket->save()){
                                    if($sendNotify){
                                        Notifications::model()->sendGiftTicketNotify($ticket->id,Yii::app()->user->id,$ticket->user_id);
                                    }
                                    $res = true;
                                } else {
                                    $msg = Yii::t('wonlot','Errore nel salvataggio del ticket');
                                }
                            }
                        } else {
                            $msg = Yii::t('wonlot','Email mancante');
                        }
                        break;
                    case "Facebook":
                    case "Google":
                        if(!empty($formPost['giftToUserId'])) {
                            $checkExistUserCrit = new CDbCriteria();
                            $checkExistUserCrit->addCondition('t.login_type = '.Yii::app()->params['authExtSource'][$formPost['provider']]);
                            $checkExistUserCrit->addCondition('t.ext_user_id = '.$formPost['giftToUserId']);
                            $scuser = SocialUser::model()->find($checkExistUserCrit);
                            if($scuser->user_id == Yii::app()->user->id){
                                $msg = Yii::t('wonlot','Non puoi regalare a te stesso');
                            } else {
                                $ticket->is_gift = 1;
                                if($scuser){
                                    $ticket->user_id = $scuser->id;
                                    $sendNotify = true;
                                    $toUser = $scuser->user->username;
                                } else {
                                    $ticket->gift_from_id = Yii::app()->user->id;
                                    $ticket->gift_provider = trim($formPost['provider']);
                                    $ticket->gift_ext_user = $formPost['giftToUserId'];
                                    $ticket->gift_ext_username = $formPost['giftToUsername'];
                                    $toUser = $ticket->gift_ext_username;
                                }
                                if($ticket->save()){
                                    if($sendNotify){
                                        Notifications::model()->sendGiftTicketNotify($ticket->id,Yii::app()->user->id,$ticket->user_id);
                                    }
                                    $res = true;
                                } else {
                                    $msg = Yii::t('wonlot','Errore nel salvataggio del ticket');
                                }
                            }
                        } else {
                            $msg = Yii::t('wonlot','Dati mancanti');
                        }
                        break;
                    case "Chi ti segue":
                    case "Chi segui":
                        if(!empty($formPost['giftToUserId'])) {
                            $ticket->user_id = $formPost['giftToUserId'];
                            $ticket->is_gift = 1;
                            $ticket->is_sent = 0;
                            $ticket->gift_from_id = Yii::app()->user->id;
                            if($ticket->save()){
                                $toUser = $ticket->user->username;
                                Notifications::model()->sendGiftTicketNotify($ticket->id,Yii::app()->user->id,$ticket->user_id);
                                $res = true;
                            } else {
                                $msg = Yii::t('wonlot','Errore nel salvataggio del ticket');
                            }
                        } else {
                            $msg = Yii::t('wonlot','Dati mancanti');
                        }
                        break;
                    default:
                        break;
                }
            }
            return array('res'=>$res,'msg'=>$msg,'toUser'=>$toUser);
        }
        public function actionGiftTicket(){
            $this->buyTicket($_POST['GiftForm'], true);         
        }
        
        public function actionBuyTicket(){
            $this->buyTicket($_POST['BuyForm']);
        }
        
        public function buyTicket($formPost, $isGift = false)
        {
            Yii::app()->clientScript->scriptMap['jquery.js'] = false;
            Yii::log('BuyStart','error');
            $data = array();
            $data["result"] = "0";
            $rollback=false;
            $skipEnd = false;
            $lotId = isset($formPost['lotId']) ? $formPost['lotId'] : null;
            if($lotId){
                $lot=Auctions::model()->findByAttributes(array('id'=>$lotId),'status=:status',array(':status'=>Yii::app()->params['lotteryStatusConst']['open']));
            } else {
                $data["msg"] = Yii::t('wonlot','ID Asta mancante');
            }
            if(!$lot){
                $data["msg"] = Yii::t('wonlot','Stato della Asta errato');
            } else {
                $user=Users::model()->findByPk(Yii::app()->user->id);
                $newPrice = $lot->ticket_value;
                $promotion = null;
                // calculate value with discount
                if($formPost['offerId'] > 0){
                    $offer = UserSpecialOffers::model()->findByPk($formPost['offerId']);
                    if($offer->times_remaining > 0 && $offer->offer_on == UserSpecialOffers::onTicketBuy){
                        $newPrice = $lot->ticket_value - ($lot->ticket_value * (int)$offer->offer_value / 100);
                        $promotion = $formPost['offerId'];
                    } 
                }
                
                //check if user has credit
                if($user->available_balance_amount < $newPrice){
                    $data["msg"] = "Not enough credit";
                } else {
                    $ticket=new Tickets;
                    $ticket->user_id=Yii::app()->user->id;
                    $ticket->lottery_id=$lot->id; 
                    
                    $ticket->serial_number=Auctions::model()->genRandomTicketSerial(); 
                    $ticket->random_weight=$this->genRandomWeight(); 
                    $checkSerial=true;
                    
                    while ($checkSerial){
                        $criteria=new CDbCriteria; 
                        $criteria->addCondition('lottery_id='.$lot->id);
                        $criteria->addCondition('serial_number='.$ticket->serial_number);
                        $existTicket=Tickets::model()->findAll($criteria);
                        if($existTicket){
                            $ticket->serial_number=Auctions::model()->genRandomTicketSerial(); 
                        } else {
                            $checkSerial=false;
                        }
                    }
                    $dbTransaction=$ticket->dbConnection->beginTransaction();
                    // to add promotions mng
                    $ticket->price=$newPrice; // change with payed price (value - promotion)
                    $ticket->value=$lot->ticket_value; 
                    $ticket->promotion_id=$promotion; 
                    // give ticket as GIFT
                    if($isGift){
                        $giftRes = $this->setGift($ticket,$formPost);
                        $data["social"] = $formPost;
                        if(!$giftRes['res']){
                            $dbTransaction->rollback(); 
                            $data["msg"] = $giftRes['msg'];
                            $skipEnd = true;
                        }
                    }
                    if(!$skipEnd){
                        $lotStatus=array_search($lot->status, Yii::app()->params['lotteryStatusConst']);
                        if(in_array($lotStatus,array('upcoming','open'))){
                            $ticket->status=Yii::app()->params['ticketStatusConst']['open'];
                        }
                        if($ticket->save()){

                            // fund down on user
                            $user->available_balance_amount-=$ticket->price;
                            if($promotion){
                                $offer->times_remaining -= 1; 
                                if(!$offer->save()){
                                   $dbTransaction->rollback(); 
                                   $data["msg"] = Yii::t('wonlot','Errore nel salvataggio delle offerte speciali');
                                   $rollback=true;
                                }
                            }
                            if($user->save() && !$rollback){

                                //transaction tracking
                                if(UserTransactions::model()->addBuyTicketTrans($ticket->id,$ticket->price,$promotion)){
                                    $lot->ticket_sold+=1;
                                    $lot->ticket_sold_value+=$ticket->price;
                                    $lot->save();
                                    $winRes = $lot->updateWinning();
                                    $dbTransaction->commit();
//                                    $checkRes=$lot->checkNewStatus();
                                    $data["result"] = "1";
                                    if($isGift){
                                        $data["msg"] = Yii::t('wonlot','Il biglietto n° ').$ticket->serial_number.Yii::t('wonlot'," è stato regalato a ").$giftRes['toUser'];
                                    } else {
                                        $showWeight = $ticket->random_weight - 1;
                                        $data["msg"] = Yii::t('wonlot','Il biglietto n° ').$ticket->serial_number.Yii::t('wonlot'," è tuo e vale 1 WCredit + {$showWeight} WCredit Bonus");
                                    }
                                    $data["winRes"] = $winRes;
                                } else {
                                    $dbTransaction->rollback();
                                    $data["msg"] = Yii::t('wonlot','Errore nel salvataggio della transazione');
                                }

                            } else {
                                $dbTransaction->rollback();
                                $data["msg"] = Yii::t('wonlot','Errore nel salvataggio dell\'addebito');
                            }
                        } else {
                            $data["msg"] = Yii::t('wonlot','Errore nella creazione del ticket');
                        }
                    }
                }
            }
            
            $this->ticketTotals = Tickets::model()->getMyTicketsByLottery($lotId);
            $this->actualWeight = Tickets::model()->getMyTotalForLottery($lotId);
            $this->giftTicketTotals=Tickets::model()->getMyGiftTicketsByLottery($lotId);
            $data["ticketNumber"] = $ticket->id;
            $data["canBuyAgain"] = ($checkRes && $checkRes == Yii::app()->params['lotteryStatusConst']['closed']) ? 0 : 1;
            $data["version"] = 'complete';
            $data["data"] = $lot;
            $data["ticket"] = $ticket;
            $data["offerId"] = $formPost['offerId'];
            if($isGift){
                $this->renderPartial('_giftAjax', $data, false, true);
            } else {
                $this->renderPartial('_buyAjax', $data, false, true);
            }
            Yii::log('BuyEND','error');
            
        }

        public function actionSetDefault()
        {
            $imgName=$_GET['img'];
            $lotId=$_GET['lotId'];
            $data = array();
            //check ownership
            $lottery=$this->loadModel($lotId);
            if($lottery->owner_id === Yii::app()->user->id){
                $lottery->prize_img=$imgName;
                if($lottery->save()){
                    $data["type"] = "alert alert-success";
                    $data["result"] = "1";
                    $data["msg"] = "Image set ";
                } else {
                    $data["type"] = "alert alert-error";
                    $data["result"] = "0";
                    $data["msg"] = "Error ";
                }
            } else {
                $data["type"] = "alert alert-error";
                $data["result"] = "0";
                $data["msg"] = "Not owner";
            }
            $data['data']=$lottery;
            $this->renderPartial('_setDefaultImage', $data, false, true);
        }
        
        public function actionDeleteImg()
        {
            $data = array();
            $data['id']=$_GET['lotId'];
            $filePath="images/auctions/".$_GET['lotId']."/".$_GET['img'];
            if (is_file($filePath)) {
                $success = unlink($filePath);
                if ($success) {
                    $image_versions = Yii::app()->params['image_versions'];
                    foreach($image_versions as $version => $options) {
                        $thumbPath = "images/auctions/".$_GET['lotId']."/".$version."/".$_GET['img'];
                        try{
                            $success = unlink($thumbPath);
                        } catch(Exception $e){
                            $t=$e->getMessage();
                        }
                    }
//                    $data["type"] = "alert alert-success";
                    $data["result"] = "1";
//                    $data["msg"] = "Image deleted";
                } else {
//                    $data["type"] = "alert alert-error";
                    $data["result"] = "0";
//                    $data["msg"] = "Image not deleted";
                }
            }
            
            $this->renderPartial('_setDefaultImage', $data, false, true);
        }

        protected function userCanBuy($lotId){
            $lot = $this->loadModel($lotId);
            if(Yii::app()->user->isGuest())
                return Auctions::errorGuest;
            if(Yii::app()->user->id === $lot->owner_id)
                return Auctions::errorOwner;
            $user = Users::model()->findByPk(Yii::app()->user->id);
            $userCredit = $user->available_balance_amount;
            //check for lottery status
            $lotteryStatusConst = Yii::app()->params['lotteryStatusConst'];
            if(!in_array($lot->status, array($lotteryStatusConst['open'],$lotteryStatusConst['active']))){
                return Auctions::errorStatus;
            }
            //check for credit  TODO: add check for discount (adapt check balance with use of discounts)
            if($userCredit < $lot->ticket_value){
                $checkCreditOk = false;
                foreach($user->offers as $off){
                    if($off->offer_on == Yii::app()->params['specialOffersCode']['ticket-buy']){
                        $newPrice = $lot->ticket_value - ($lot->ticket_value * (int)$off->offer_value / 100);
                        if($newPrice <= $userCredit){
                            $checkCreditOk = true;
                            break;
                        }
                    }
                }
                if(!$checkCreditOk){
                    return Auctions::errorCredit;
                }
            }
            return true;
        }
        
        /**
	 * Performs the AJAX validation.
	 * @param Auctions $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='auctions-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
        
        private function filterAuctions($my=0,$type="dataProvider") {
            $filter=array();
            $result = array();
            $result['viewData']=array();
            if(!empty($_POST['SearchForm']['Category']) && empty($_POST['SearchForm']['Categories'])){
                $filter["prizeCategory"]=$_POST['SearchForm']['Category'];
                $result['viewData']['showCat']=$_POST['SearchForm']['Category'];
            }
            if(!empty($_POST['SearchForm']['Categories'])){
                $filter["prizeCategory"]=$_POST['SearchForm']['Categories'];
                $result['viewData']['showCat']=$_POST['SearchForm']['Categories'];
            }
            if(empty($_POST['SearchForm']['LotStatus']) && !empty($_POST['SearchForm']['LotStartStatus'])){
                $_POST['SearchForm']['LotStatus'][]=$_POST['SearchForm']['LotStartStatus'];
            }
            if(!empty($_POST['SearchForm']['LotStatusComplete'])){
                $statusOptions = $_POST['SearchForm']['LotStatusComplete'];
                $first = true;
                $filter['status'] = array();
                foreach($statusOptions as $opt) {
                    $filter['status'] = array_merge($filter['status'],array(Yii::app()->params['lotteryStatusConst'][$opt])); 
                    $result['viewData']['showStatus'].=($first?"":", ").Yii::app()->params['lotteryStatusConstIta'][$opt];
                    $first=false;
                }
            }
            if(!empty($_POST['SearchForm']['LotStatus'])){
                $statusOptions = $_POST['SearchForm']['LotStatus'];
                $first = true;
                $filter['status'] = array();
                foreach($statusOptions as $opt) {
                    if($opt == '1'){
                       $filter['status'] = array_merge($filter['status'],array(3)); 
                       $result['viewData']['showStatus'].=($first?"":", ").array_search(3, Yii::app()->params['lotteryStatusConst']);
                       $first=false;
                    }
                    if($opt == '2'){
                       $filter['status'] = array_merge($filter['status'],array(2)); 
                       $result['viewData']['showStatus'].=($first?"":", ").array_search(2, Yii::app()->params['lotteryStatusConst']);
                       $first=false;
                    }
                    if($opt == '3'){
                       $filter['status'] = array_merge($filter['status'],array(4,5)); 
                       $result['viewData']['showStatus'].=($first?"":", ").array_search(4, Yii::app()->params['lotteryStatusConst']);
                       $result['viewData']['showStatus'].=", ".array_search(5, Yii::app()->params['lotteryStatusConst']);
                       $first=false;
                    }
                }
            }
            /*if(!empty($_POST['SearchForm']['searchStartDate'])){
                $filter["minDate"]=Yii::app()->dateFormatter->format('dd-MM-yyyy',$_POST['SearchForm']['searchStartDate']);
            }
            if(!empty($_POST['SearchForm']['searchEndDate'])){
                $filter["maxDate"]=Yii::app()->dateFormatter->format('dd-MM-yyyy',$_POST['SearchForm']['searchEndDate']);
            }
            if(!empty($_POST['SearchForm']['lottery_start_date'])){
                $filter["startDate"]=Yii::app()->dateFormatter->format('dd-MM-yyyy',$_POST['SearchForm']['lottery_start_date']);
            }
            if(!empty($_POST['SearchForm']['lottery_draw_date'])){
                $filter["endDate"]=Yii::app()->dateFormatter->format('dd-MM-yyyy',$_POST['SearchForm']['lottery_draw_date']);
            }*/
            if(!empty($_POST['SearchForm']['searchStartDate'])){
                $filter["minDate"]=Yii::app()->dateFormatter->format('yyyy-MM-dd',$_POST['SearchForm']['searchStartDate']);
            }
            if(!empty($_POST['SearchForm']['searchEndDate'])){
                $filter["maxDate"]=Yii::app()->dateFormatter->format('yyyy-MM-dd',$_POST['SearchForm']['searchEndDate']);
            }
            if(!empty($_POST['SearchForm']['lottery_start_date'])){
                $filter["startDate"]=Yii::app()->dateFormatter->format('yyyy-MM-dd',$_POST['SearchForm']['lottery_start_date']);
            }
            if(!empty($_POST['SearchForm']['lottery_draw_date'])){
                $filter["endDate"]=Yii::app()->dateFormatter->format('yyyy-MM-dd',$_POST['SearchForm']['lottery_draw_date']);
            }
            if(!empty($_POST['SearchForm']['minTicketPriceRange'])){
                $filter["minTicketPriceRange"]=$_POST['SearchForm']['minTicketPriceRange'];
            }
            if(!empty($_POST['SearchForm']['maxTicketPriceRange'])){
                $filter["maxTicketPriceRange"]=$_POST['SearchForm']['maxTicketPriceRange'];
            }
            /*if(!empty($_POST['SearchForm']['minPrizePriceRange'])){
                $filter["minPrizePriceRange"]=$_POST['SearchForm']['minPrizePriceRange'];
            }
            if(!empty($_POST['SearchForm']['maxPrizePriceRange'])){
                $filter["maxPrizePriceRange"]=$_POST['SearchForm']['maxPrizePriceRange'];
            }*/
            if($_POST['SearchForm']['geoLat'] && $_POST['SearchForm']['geoLng']){
//                $re = Locations::model()->orderByDistance(array('addressLat' => $_POST['SearchForm']['geoLat'],'addressLng' => $_POST['SearchForm']['geoLng']));
                $filter["geo"]=array('lat'=>$_POST['SearchForm']['geoLat'], 'lng'=>$_POST['SearchForm']['geoLng']);
            }
            if($_POST['SearchForm']['searchText']){
                $filter["searchText"]=$_POST['SearchForm']['searchText'];
            }
            if($_POST['SearchForm']['name']){
                $filter["name"]=$_POST['SearchForm']['name'];
            }
            if($_POST['SearchForm']['prize_desc']){
                $filter["prize_desc"]=$_POST['SearchForm']['prize_desc'];
            }
            if($_POST['SearchForm']['favorite']){
                $filter["favorite"]=$_POST['SearchForm']['favorite'];
            }
            if($_POST['SearchForm']['userGuaranted']){
                $filter["searchText"]=$_POST['SearchForm']['userGuaranted'];
            }
            if($_POST['SearchForm']['userMinRating']){
                $filter["userMinRating"]=$_POST['SearchForm']['userMinRating'];
            }
            if($_POST['SearchForm']['onlyCompany']){
                $filter["onlyCompany"]=$_POST['SearchForm']['onlyCompany'];
            }
            if($_POST['SearchForm']['onlyPrivate']){
                $filter["onlyPrivate"]=$_POST['SearchForm']['onlyPrivate'];
            }
            if($_POST['SearchForm']['onlyNew']){
                $filter["onlyNew"]=$_POST['SearchForm']['onlyNew'];
            }
            if($_POST['SearchForm']['onlyUsed']){
                $filter["onlyUsed"]=$_POST['SearchForm']['onlyUsed'];
            }
            if($my)
                $filter["my"]="true";
            
            if($type=="dataProvider"){
                $result['dataProvider']=Auctions::model()->getAuctions($filter);
                return $result;
            } elseif($type=="activeRecord"){
                $result=Auctions::model()->getAuctions($filter,"pager");
                return $result;
            }
        }
        
        private function _editLottery($model){
            Yii::import("xupload.models.XUploadForm");
            $upForm = new XUploadForm;
            $this->upForm=$upForm;
            $this->locationForm=new Locations;
            $isOld=$model->id;
            $model->scenario = "editSubmit";
            if($model->location_id){
                $existLoc=Locations::model()->findByPk($model->location_id);
                if($existLoc)
                    $this->locationForm=$existLoc;
            } 

            if(isset($_POST['Auctions']))
            {
                $model->attributes=$_POST['Auctions'];
                $model->owner_id=Yii::app()->user->id;
                //check for dates
                /*$now=date("d/m/Y h:m");
                if($model->lottery_start_date < $now){
                    $model->lottery_start_date
                }*/
                if($_POST['filename'][0]){
                    if($_POST['isdefault'] && isset($_POST['isdefault'][0])){
                        $model->prize_img=$_POST['filename'][$_POST['isdefault'][0]];
                    } else {
                        $model->prize_img=$_POST['filename'][0];
                    }
                }
                if($_POST['Auctions']['prize_price']){
                    $model->max_ticket = ceil($_POST['Auctions']['prize_price'] / $model->ticket_value);
                }
                if($_POST['Locations'] && !empty($_POST['Locations']['addressLat']) && !empty($_POST['Locations']['addressLng'])){
                    //check if Location exist
                    $model->location_id = $this->saveLocation($_POST['Locations']);
                }
                if($_POST['publish']){
                    $model->status=Yii::app()->params['lotteryStatusConst']['upcoming'];
                } 
                if(!$model->status) {
                    $model->status=Yii::app()->params['lotteryStatusConst']['draft'];
                }
                if($model->save()){
                    $this->renameTmpFolder($model->id);
                    if($model->cloneId){
                        $this->copyCloneFolder('lottery',$model->cloneId,$model->id);
                    }
//                        if($isOld){
//                            $this->redirect(array('update','id'=>$model->id));
//                        } else {
                        $this->redirect(array('view','id'=>$model->id));
//                        }
                }
            } else {
                $this->cleanTmpFolder();
            }

            $this->render('update', array(
                    'model' => $model,
            ));
        }
        
        protected function checkGiftStatus($data,$row)
        {
             // ... generate the output for the column

             // Params:
             // $data ... the current row data   
            // $row ... the row index    
            $res = "";
            if($data->is_gift){
                $res .= '<p class="bg-success">Regalato!</p>';
            } else {
                $res .= '<button id="'.$data->id.'" class="btn btn-success btn-xs set-gift"><i class="glyphicon glyphicon-search">Regala</i></button>';
            }
            return $res;    
       }
       
       private function genRandomWeight()
       {
		$weibull = new \PHPStats\ProbabilityDistribution\Weibull($this->randValues['lambda'],$this->randValues['k']);
                $rnd = ceil($weibull->rvs());
                if($rnd > 100) $rnd = $rnd%100;
                return $rnd;
       }
}
