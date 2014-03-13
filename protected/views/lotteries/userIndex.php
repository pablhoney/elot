<?php 
$h1="<h1>Lotteries</h1>";
if($viewData['showStatus']){
//   $h1.=" <h3>(".CHtml::encode(Yii::app()->params['lotteryStatusConst'][$viewData['showStatus']]).")</h3>"; 
   $h1.=" <h3>Status: ".CHtml::encode($viewData['showStatus'])."</h3>"; 
}
if($viewData['showCat']){
   $h1.=" <h3>Category: ".CHtml::encode(PrizeCategories::model()->getPrizeCatNameById($viewData['showCat']))."</h3>"; 
}
echo $h1;
if(!Yii::app()->user->isGuest){
    echo CHtml::link("New Lottery",CController::createUrl('lotteries/create'),array('class'=>'btn')); 
}
?>

<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$dataProvider,
//        'itemView'=>'/lotteries/my-lot-box',
        'enablePagination'=>true,
        'enableSorting'=>true,
        'columns'=>array(
            'name',          // display the 'title' attribute
            'category.category_name',  // display the 'name' attribute of the 'category' relation
            'prize_desc:html',   // display the 'content' attribute as purified HTML
            array(            // display 'create_time' using an expression
                'name'=>'lottery_start_date',
//                'value'=>'date("M j, Y", $data->lottery_start_date)',
                'value'=>'$data->lottery_start_date',
            ),
            array(            // display 'create_time' using an expression
                'name'=>'lottery_draw_date',
                'value'=>'$data->lottery_draw_date',
            ),
            array(            // display 'create_time' using an expression
                'name'=>'prize_img',
//                'type'=>'image',
                'type'=>'html',
                'value'=>'CHtml::image(Controller::getImageUrl($data,"smallSquaredThumb"),"Foto Premio")',
            ),
            
            array(            // display a column with "view", "update" and "delete" buttons
                'class'=>'CButtonColumn',
            ),
        ),
    ));
    ?>

    <?php
    /*$this->widget('ext.isotope.Isotope',array(
        'dataProvider'=>$dataProvider,
        'itemView'=>'lot-box',
        'viewData'=>$viewData,
        'itemSelectorClass'=>'isotope-item',
        'options'=>array( // options for the isotope jquery
            'layoutMode'=>'masonry',
            'containerStyle' => array(
                'position' => 'relative', 'overflow' => 'hidden'
            ),
            'animationEngine'=>'jquery',
            'animationOptions'=>array(
                    'duration'=>300,
            ),
        ), 
        'infiniteScroll'=>true, // default to true
        'infiniteOptions'=>array(), // javascript options for infinite scroller
        'id'=>'wall',
    ));*/
     ?>
     