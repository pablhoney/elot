<div class="modal fade" id="gift-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<?php $checkBuy = $this->userCanBuy($data->id); ?>  
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo Yii::t('wonlot','Regala Ticket'); ?><span id="gift-to-header"></span></h4>
      </div>
      <div class="modal-body">
            <?php if($checkBuy === true){ ?>                    
                <div class="center-float">
                    <?php 
                        $addData = array('version' => 'complete', 'data' => $data);
                        $this->renderPartial('_giftAjax', $addData); 
                        ?>
                </div>
                    
            <?php } elseif($checkBuy == Lotteries::errorGuest) { ?>
                <h4>LOGIN TO BUY!</h4>
                <?php $this->renderPartial('/site/login',array('showLogin'=>true)); ?>
            <?php } elseif($checkBuy == Lotteries::errorStatus) { ?>
                <h4>LOTTERY IN WRONG STATUS</h4>
            <?php } elseif($checkBuy == Lotteries::errorCredit) { ?>
                <h4>NOT ENOUGH CREDIT!</h4>
            <?php } elseif($checkBuy == Lotteries::errorOwner) { ?>
                <h4>CANNOT BUY YOUR LOTTERY!</h4>
            <?php } ?>
      </div>
      <div class="modal-footer">
          <?php echo CHtml::ajaxButton ("Regala!",
            CController::createUrl('auctions/giftTicket'), 
            array(
              'update' => '#gift-main',
              'type' => 'POST', 
              'data'=>'js:$("#giftLotteryForm").serialize()',
            ),
            array('name'=>'giftBtn', 'class'=>'btn btn-primary', 'disabled'=>($checkBuy ? '' : 'disabled'))
            ); ?>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('wonlot','Chiudi'); ?></button>
      </div>
    </div>
  </div>
</div>