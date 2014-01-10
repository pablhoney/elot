<?php
/* @var $this LotteriesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Lotteries',
);

?>
<?php 
$h1="<h1>Lotteries</h1>";
if($viewData['showStatus']){
//   $h1.=" <h3>(".CHtml::encode(Yii::app()->params['lotteryStatusConst'][$viewData['showStatus']]).")</h3>"; 
   $h1.=" <h3>(".CHtml::encode($viewData['showStatus']).")</h3>"; 
}
if($viewData['showCat']){
   $h1.=" <h3>(".CHtml::encode(PrizeCategories::model()->getPrizeCatNameById($viewData['showCat'])).")</h3>"; 
}
echo $h1;
if(!Yii::app()->user->isGuest){
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'label' => 'New Lottery',
            'type' => 'primary',
            'url' => CController::createUrl('lotteries/create'),
        )
    ); 
}
?>

<!--<div class="btn-group">
  <button type="button" class="btn btn-danger">Action</button>
  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
    <span class="caret"></span>
    <span class="sr-only">Toggle Dropdown</span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <?php $cat=new PrizeCategories();
        echo CHtml::activeCheckboxList(
            $cat, 'id', 
            CHtml::listData(PrizeCategories::model()->findAll(), 'id', 'category_name'),
            array('template'=>'<li>{input} {label}</li>',)
    );?>
  </ul>
</div>-->

    <?php
    $this->widget('ext.isotope.Isotope',array(
        'dataProvider'=>$dataProvider,
        'itemView'=>$viewType,
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
    ));?>