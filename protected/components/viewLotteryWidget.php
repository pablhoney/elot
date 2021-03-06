<?php
class viewLotteryWidget extends CWidget
{
    public $lotController;
    public $model;
    public function run()
    {
        $this->lotController = Yii::app()->createController('auctions');
        $this->lotController = $this->lotController[0];
        if(!Yii::app()->user->isGuest){
            list($this->lotController->ticketTotals,$actualStatus)=Tickets::model()->getMyTicketsByLottery($this->model->id);
        }
        $this->render('viewLottery',array('model'=>Auctions::model()->findByPk($this->model->id)));
    }
}
?>
