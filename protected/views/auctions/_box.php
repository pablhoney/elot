<?php
/* @var $this AuctionsController */
/* @var $data Auctions */
$boxTitle="<b>".CHtml::link($data->name, CController::createUrl('auctions/view/'.$data->id))."</b>";
/*if($showCat){
    $boxTitle.=" - ".CHtml::encode(PrizeCategories::model()->getPrizeCatNameById($data->prize_category));
}*/
?>
<div class="panel panel-default bootstrap-widget-table lot-box">
    <div class="panel-heading">
      <h3 class="panel-title"><?php echo $boxTitle;?></h3>
    </div>
    <div class="panel-body">
        <div class="lot-box-img">
            <?php if($data->prize_img){ ?>
                <div class=""><?php echo CHtml::image($this->getImageUrl($data ,'mediumSquaredThumb'),'Lottery Image'); ?></div>
            <?php } ?>
        </div>

        <div class="lot-box-body">
            <div class=""><?php echo CHtml::encode(Yii::app()->params['lotteryTypes'][$data->lottery_type]['name']); ?></div>
            <div class=""><b>Prize:</b><?php echo $data->prize_desc; ?></div>
            <div class=""><b>Category:</b><?php echo CHtml::encode(PrizeCategories::model()->getPrizeCatNameById($data->prize_category)); ?></div>
            <div class=""><b>Tickets</b></div>
            <div class="">Value: <?php echo CHtml::encode($data->ticket_value); ?> euro</div>
            <div class="">Total: <?php echo CHtml::encode($data->max_ticket); ?></div>
            <div class="">Sold: <?php echo CHtml::encode($data->ticket_sold); ?></div>
            <div class="">Remaining: <?php echo CHtml::encode($data->max_ticket-$data->ticket_sold); ?></div>
        </div>
    </div>
    <div class="panel-footer">
        <?php if($data->lottery_type === Yii::app()->params['lotteryTypesConst']['fixTime']){ ?>
                <div class=""><b>Lottery draw date:</b><?php echo CHtml::encode($data->lottery_draw_date); ?></div>
        <?php } ?>
                <button type="button" class="btn btn-primary"><?php echo CHtml::link('Edit', CController::createUrl('auctions/update/'+$data->id));?></button>
    </div>
</div>