<?php

class LotteriesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/basecolumn';
        
        public $ticketTotals;
        
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
				'actions'=>array('index','view','category'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','userIndex','upload','buyTicket','setDefault','deleteImg'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete','update'),
                                'expression' => array('LotteriesController','allowOnlyOwner')
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin'),
                                'expression' => array('LotteriesController','isAdmin')
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
//                $this->layout="//layouts/allpage";
                if(!Yii::app()->user->isGuest){
                    $this->ticketTotals=Tickets::model()->getMyTicketsNumberByLottery($id);
                }
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
                $this->layout='column2l6l4';
                $this->sideView='createLotteyHelp';
                $model=new Lotteries;
                $this->_editLottery($model);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
                $model = $this->loadModel($id);
                $this->_editLottery($model);
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */ 
	public function actionIndex()
	{
            if($_POST['reset']){
                $_POST['SearchForm'] = null;
                unset(Yii::app()->session['filters']);
            } 
            if($_GET['cat']){
                $cat = PrizeCategories::model()->findByAttributes(array('category_name'=>$_GET['cat']));
                if($cat){
                    $_POST['SearchForm']['Categories']=$cat->id;
                }
            }
            if($_POST['SearchForm']){
                Yii::app()->session['filters'] = $_POST['SearchForm'];
            } else {
                $_POST['SearchForm'] = Yii::app()->session['filters'];
                if($_POST['SearchForm']){
                    $_POST['SearchForm']['status'] = array(3,4);
                }
            }
            $lotteries=$this->filterLotteries();
            if(!Yii::app()->user->isGuest){
                $this->ticketTotals=Tickets::model()->getMyTicketsNumberAllLotteries();
            }
            $this->render('index',array(
                'dataProvider'=>$lotteries['dataProvider'],
                'viewType'=>'_show',
                'viewData'=>$lotteries['viewData'],
            ));
	}

	/**
         * Load user Lotteries
         */
        public function actionUserIndex()
	{
            $lotteries=$this->filterLotteries(true);
            $this->ticketTotals=Tickets::model()->getMyTicketsNumberAllLotteries();
            $this->render('index',array(
                'dataProvider'=>$lotteries['dataProvider'],
                'viewType'=>"_box",
                'viewData'=>$lotteries['viewData'],
            ));
	}
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Lotteries('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Lotteries']))
			$model->attributes=$_GET['Lotteries'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Lotteries the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Lotteries::model()->findByPk($id);
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
        public function actionBuyTicket()
        {
            $data = array();
            $data["type"] = "alert alert-error";
            $data["result"] = "ERROR - ";
            $lotId = isset($_POST['BuyForm']['lotId']) ? $_POST['BuyForm']['lotId'] : null;
            if($lotId){
                $lot=Lotteries::model()->findByAttributes(array('id'=>$lotId),'status=:status',array(':status'=>Yii::app()->params['lotteryStatusConst']['open']));
            } else {
                $data["msg"] = "Lottery id is missing";
            }
            if(!$lot){
                $data["msg"] = "Lottery in wrong status";
            } else {
                $user=Users::model()->findByPk(Yii::app()->user->id);
                $newPrice = $lot->ticket_value;
                $promotion = null;
                // calculate value with discount
                if($_POST['BuyForm']['offerId']){
                    $offer = UserSpecialOffers::model()->findByPk($_POST['BuyForm']['offerId']);
                    if($offer->times_remaining > 0 && $offer->offer_on == UserSpecialOffers::onTicketBuy){
                        $newPrice = $lot->ticket_value - ($lot->ticket_value * (int)$offer->offer_value / 100);
                        $promotion = $_POST['BuyForm']['offerId'];
                    } 
                }
                //check if user has credit
                if($user->available_balance_amount < $newPrice){
                    $data["msg"] = "Not enough credit";
                } else {
                    $ticket=new Tickets;
                    $ticket->user_id=Yii::app()->user->id;
                    $ticket->lottery_id=$lot->id; 
                    $ticket->serial_number=rand(100000,999999); 
                    // TODO: add serial number uniqueness! find(sameLot, sameNumber)
                    $checkSerial=true;
                    while ($checkSerial){
                        $criteria=new CDbCriteria; 
                        $criteria->addCondition('lottery_id='.$lot->id);
                        $criteria->addCondition('serial_number='.$ticket->serial_number);
                        $existTicket=Tickets::model()->findAll($criteria);
                        if($existTicket){
                            $ticket->serial_number=rand(100000,999999); 
                        } else {
                            $checkSerial=false;
                        }
                    }
                    // to add promotions mng
                    $ticket->price=$newPrice; // change with payed price (value - promotion)
                    $ticket->value=$lot->ticket_value; 
                    $ticket->promotion_id=$promotion; 
                    $lotStatus=array_search($lot->status, Yii::app()->params['lotteryStatusConst']);
                    if(in_array($lotStatus,array('upcoming','open'))){
                        $ticket->status=Yii::app()->params['ticketStatusConst']['open'];
                    }
                    if($lotStatus === 'active'){
                        $ticket->status=Yii::app()->params['ticketStatusConst']['active'];
                    }
                    $dbTransaction=$ticket->dbConnection->beginTransaction();
                    if($ticket->save()){
                        // fund down on user
                        $user->available_balance_amount-=$ticket->price;
                        if($promotion){
                            $offer->times_remaining -= 1; 
                            if($offer->save()){
                                if($user->save()){
                                    //transaction tracking
                                    if(UserTransactions::model()->addBuyTicketTrans($ticket->id,$ticket->price)){
                                        $lot->ticket_sold+=1;
                                        $lot->ticket_sold_value+=$ticket->price;
                                        $lot->save();
                                        $dbTransaction->commit();
                                        $lot->checkNewStatus();
                                        $data["type"] = "alert alert-success";
                                        $data["result"] = "OK!";
                                        $data["msg"] = "Best buy!";
                                    } else {
                                        $dbTransaction->rollback();
                                        $data["msg"] = "saving user transaction";
                                    }
                                } else {
                                    $dbTransaction->rollback();
                                    $data["msg"] = "witdrawing to user";
                                }
                            } else {
                               $dbTransaction->rollback(); 
                               $data["msg"] = "saving user special offer";
                            }
                        }
                    } else {
                        $data["msg"] = "creating ticket";
                    }
                }
            }
            $this->ticketTotals=Tickets::model()->getMyTicketsNumberByLottery($lotId);
            $data["id"] = $lot->id;
            $data["lottery"] = $lot;
            $data["version"] = 'complete';
            $this->renderPartial('_buyAjax', $data, false, true);
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
                    $data["msg"] = "Image set a default";
                } else {
                    $data["type"] = "alert alert-error";
                    $data["result"] = "0";
                    $data["msg"] = "Error saving lottery";
                }
            } else {
                $data["type"] = "alert alert-error";
                $data["result"] = "0";
                $data["msg"] = "Not owner of lottery";
            }
            $data['data']=$lottery;
            $this->renderPartial('_setDefaultImage', $data, false, true);
        }
        
        public function actionDeleteImg()
        {
            $data = array();
            $data['id']=$_GET['lotId'];
            $filePath="images/lotteries/".$_GET['lotId']."/".$_GET['img'];
            if (is_file($filePath)) {
                $success = unlink($filePath);
                if ($success) {
                    $image_versions = Yii::app()->params['image_versions'];
                    foreach($image_versions as $version => $options) {
                        $thumbPath = "images/lotteries/".$_GET['lotId']."/".$version."/".$_GET['img'];
                        $success = unlink($filePath);
                    }
                    $data["type"] = "alert alert-success";
                    $data["result"] = "1";
                    $data["msg"] = "Image deleted";
                } else {
                    $data["type"] = "alert alert-error";
                    $data["result"] = "0";
                    $data["msg"] = "Image not deleted";
                }
            }
            
            $this->renderPartial('_setDefaultImage', $data, false, true);
        }

        protected function userCanBuy($lotId){
            $lot = $this->loadModel($lotId);
            if(Yii::app()->user->isGuest())
                return false;
            if(Yii::app()->user->id === $lot->owner_id)
                return false;
            $user = Users::model()->findByPk(Yii::app()->user->id);
            $userCredit = $user->available_balance_amount;
            //check for lottery status
            $lotteryStatusConst = Yii::app()->params['lotteryStatusConst'];
            if(!in_array($lot->status, array($lotteryStatusConst['open'],$lotteryStatusConst['active']))){
                return Lotteries::errorStatus;
            }
            //check for credit  TODO: add check for discount (adapt check balance with use of discounts)
            /*if($userCredit < $lot->ticket_value){
                return Lotteries::errorCredit;
            }*/
            return true;
        }

        /**
	 * Performs the AJAX validation.
	 * @param Lotteries $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='lotteries-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
        
        private function filterLotteries($my=0) {
            $filter=array();
            $result = array();
            $result['viewData']=array();
            if(!empty($_POST['SearchForm']['Categories'])){
                $filter["prizeCategory"]=$_POST['SearchForm']['Categories'];
                $result['viewData']['showCat']=$_POST['SearchForm']['Categories'];
            }
            if(!empty($_POST['SearchForm']['LotStartStatus'])){
                $_POST['SearchForm']['LotStatus'][]=$_POST['SearchForm']['LotStartStatus'];
            }
            if(!empty($_POST['SearchForm']['LotStatus'])){
                $statusOptions = $_POST['SearchForm']['LotStatus'];
                $first = true;
                $filter['status'] = array();
                foreach($statusOptions as $opt) {
                    if($opt === '1'){
                       $filter['status'] = array_merge($filter['status'],array(3,4)); 
                    }
                    if($opt === '2'){
                       $filter['status'] = array_merge($filter['status'],array(2)); 
                    }
                    if($opt === '3'){
                       $filter['status'] = array_merge($filter['status'],array(5,6)); 
                    }
                }
                $result['viewData']['showStatus'].=($first?"":", ").array_search($opt, Yii::app()->params['lotteryStatusConst']);
                $first=false;
            }
            if(!empty($_POST['SearchForm']['searchStartDate'])){
                $filter["minDate"]=Yii::app()->dateFormatter->format('dd-MM-yyyy',$_POST['SearchForm']['searchStartDate']);
            }
            if(!empty($_POST['SearchForm']['searchEndDate'])){
                $filter["maxDate"]=Yii::app()->dateFormatter->format('dd-MM-yyyy',$_POST['SearchForm']['searchEndDate']);
            }
            if(!empty($_POST['SearchForm']['minTicketPriceRange'])){
                $filter["minTicketPriceRange"]=$_POST['SearchForm']['minTicketPriceRange'];
            }
            if(!empty($_POST['SearchForm']['maxTicketPriceRange'])){
                $filter["maxTicketPriceRange"]=$_POST['SearchForm']['maxTicketPriceRange'];
            }
            if(!empty($_POST['SearchForm']['minPrizePriceRange'])){
                $filter["minPrizePriceRange"]=$_POST['SearchForm']['minPrizePriceRange'];
            }
            if(!empty($_POST['SearchForm']['maxPrizePriceRange'])){
                $filter["maxPrizePriceRange"]=$_POST['SearchForm']['maxPrizePriceRange'];
            }
            if($_POST['SearchForm']['geo']){
                $re = Locations::model()->orderByDistance(array('addressLat' => $_POST['SearchForm']['geoLat'],'addressLng' => $_POST['SearchForm']['geoLng']));
                /*$search_address = 'Czech Republic, Prague, Olivova';

                // Create geocoded address
                $geocoded_address = new EGMapGeocodedAddress($sample_address);
                $geocoded_address->geocode($gMap->getGMapClient());

                // Center the map on geocoded address
                 $gMap->setCenter($geocoded_address->getLat(), $geocoded_address->getLng());*/
                $filter["tag"]=$_POST['SearchForm']['tag'];
            }
            if($_POST['SearchForm']['searchText']){
                $filter["searchText"]=$_POST['SearchForm']['searchText'];
            }
            if($my)
                $filter["my"]="true";
            
            
            $result['dataProvider']=Lotteries::model()->getLotteries($filter);
            return $result;
        }
        
        private function _editLottery($model){
            $form = new CForm('application.views.lotteries.form_config',$model);
            $form->showErrorSummary = true;
            Yii::import("xupload.models.XUploadForm");
            $upForm = new XUploadForm;
            $this->upForm=$upForm;
            $this->locationForm=new Locations;
            if($model->id){
                $existLoc=Locations::model()->findByPk($model->location_id);
                if($existLoc)
                    $this->locationForm=$existLoc;
            } 

            if(isset($_POST['Lotteries']))
            {
                    $model->attributes=$_POST['Lotteries'];
                    $model->owner_id=Yii::app()->user->id;
                    if(!empty($_POST['isdefault'][0])){
                        $model->prize_img=$_POST['filename'][$_POST['isdefault'][0]];
                    }
                    if($_POST['Lotteries']['prize_price']){
                        $model->max_ticket = ceil($_POST['Lotteries']['prize_price'] / $model->ticket_value);
                    }
                    if($_POST['Locations']){
                        //check if Location exist
                        $model->location_id = $this->saveLocation($_POST['Locations']);
                    }
                    if($_POST['publish']){
                        $model->status=Yii::app()->params['lotteryStatusConst']['publish'];
                        $model->is_active=1;
                    } else {
                        $model->status=Yii::app()->params['lotteryStatusConst']['upcoming'];
                    }
                    if($model->save()){
                        $this->renameTmpFolder($model->id);
                        $this->redirect(array('view','id'=>$model->id));
                    }
            } else {
                $this->cleanTmpFolder();
            }

            $this->render('update', array(
                    'form' => $form,
            ));
        }
}
