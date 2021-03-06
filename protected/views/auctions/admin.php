<?php
/* @var $this AuctionsController */
/* @var $model Auctions */

$this->breadcrumbs=array(
	'Auctions'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Auctions', 'url'=>array('index')),
	array('label'=>'Create Auctions', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#auctions-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Auctions</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php /*$this->renderPartial('_search',array(
	'model'=>$model,
));*/ ?>
</div><!-- search-form -->

<div>
    <a href="/index.php/auctions/cronLottery" class="btn btn-success">CRON</a>
</div>

<?php if($errors && $errors['count'] > 0){ ?>
    <h3>Errori CRON:</h3>
    <ul>
    <?php 
    foreach($errors as $et){ 
        foreach($et as $e){ 
            echo "<li>".$e."</li>";
        }
    } ?>
<?php } ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'auctions-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'lottery_type',
		'prize_desc',
		'prize_category',
		/*
		'prize_conditions',
		'prize_shipping',
		'prize_shipping_charges',
		'min_ticket',
		'max_ticket',
		'ticket_value',
		'lottery_start_date',
		'lottery_draw_date',
		'created',
		'modified',
		'last_modified_by',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
