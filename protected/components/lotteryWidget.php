<?php
class lotteryWidget extends CWidget
{
    public $dataProvider;
    public $tickets;
    public function init()
    {
        $this->registerScripts();
    }
 
    public function run()
    {   
        if(!Yii::app()->user->isGuest){
            $this->dataProvider = Tickets::model()->getMyTicketsProvider();
            $this->renderContent();   
        }
    }
 
    protected function renderContent()
    {
        $this->render('lotteryTable',array('tickets'=>$this->dataProvider));
    }   
    
    protected function registerScripts()
    {
        $cs = Yii::app()->getClientScript();
        ob_start();
		?>
		$(".yiiPager a").live('click', function(event){
                    var element = $(this)[0];
                    $.get(element.href).done(function( data ) {
                        $('#userTicketContainer').html(data);
                    });
                    event.preventDefault();
                });
                <?php
        $cs->registerScript(__CLASS__, ob_get_clean());
    }
}
?>
