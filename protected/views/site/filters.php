<div class="form">
    <?php
    $model = $this->filterModel;
    $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'lotSearchForm',
        'htmlOptions' => array('class' => 'well'), // for inset effect
        'enableAjaxValidation'=>true,
        'action' => $this->createUrl('lotteries/index'),
    ));
    echo $form->textFieldRow($model, 'searchText', array('class' => 'input-medium','prepend' => '<i class="icon-search"></i>', 'label' => false, 'placeholder' => "Search..."));
    if($this->id == "site"){
        $this->widget(
            'bootstrap.widgets.TbButton',
            array('buttonType' => 'submit', 'label' => 'Search')
        );
        $cat = $model->lists['Categories'];
        $box = $this->beginWidget(
            'bootstrap.widgets.TbBox',
            array(
                'title' => "<b>Categories</b>",
                'headerIcon' => 'icon-th-list',
                'htmlOptions' => array('class' => 'bootstrap-widget-table'),
            )
        );
        foreach($cat as $k=>$item){ 
            echo "<p>".CHtml::link($item, Yii::app()->createUrl('lotteries/index/'.$item), array('label' => false))."</p>";
        }
        $this->endWidget();
    } elseif($this->id == "lotteries") {
        foreach($model->lists as $title=>$items){ 
            $box = $this->beginWidget(
                'bootstrap.widgets.TbBox',
                array(
                    'title' => "<b>".$title."</b>",
                    'headerIcon' => 'icon-th-list',
                    'htmlOptions' => array('class' => 'bootstrap-widget-table'),
                )
            );
            echo $form->checkBoxListRow($model, $title, $items, array('label' => false));
            $this->endWidget();
        }
        echo $form->labelEx($model,'searchStartDate');
        $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'name' => 'startDate',
                'model' => $model,
                'attribute' => 'searchStartDate',
                'htmlOptions' => array(
                    'class' => 'input-medium',
                ),
                'options' => array(
                    'language' => 'it',
                    'dateFormat'=>'dd/mm/yy',
                    //'dateFormat'=>'yy-mm-dd',
                    'timeFormat'=>'HH:mm:ss',
                    'showSecond'=>true,
                    'showTimezone'=>false,
                    'ampm' => false,
                )
            )
        );
        echo $form->labelEx($model,'searchEndDate');
        $this->widget(
            'bootstrap.widgets.TbDatePicker',
            array(
                'name' => 'endDate',
                'model' => $model,
                'attribute' => 'searchEndDate',
                'htmlOptions' => array(
                    'class' => 'input-medium',
                ),
                'options' => array(
                    'language' => 'it',
                    'dateFormat'=>'dd/mm/yy',
                    //'dateFormat'=>'yy-mm-dd',
                    'timeFormat'=>'HH:mm:ss',
                    'showSecond'=>true,
                    'showTimezone'=>false,
                    'ampm' => false,
                )
            )
        );
        $maxPrice = Lotteries::model()->getMaxTicketPrice()+0; 
        $model->minTicketPriceRange=$model->minTicketPriceRange+0; // Trick to format decimals
        $model->maxTicketPriceRange=$model->maxTicketPriceRange+0;
        if(!$model->minTicketPriceRange){
            $model->minTicketPriceRange=0;
        }
        if(!$model->maxTicketPriceRange){
            $model->maxTicketPriceRange=$maxPrice;
        }
        echo $form->labelEx($model,'ticketPrice'); ?>
        <input type="text" class="input-medium" id="ticket-price-range" value="<?php echo $model->minTicketPriceRange . " - " . $model->maxTicketPriceRange;?>" style="border:0; color:#f6931f; font-weight:bold;" />
        <?php
        $this->widget('zii.widgets.jui.CJuiSliderInput', array(
            'name'=>'ticket_price_slider',    
            'event'=>'change',
            'model'=>$model,
            'attribute'=>'minTicketPriceRange',
            'maxAttribute'=>'maxTicketPriceRange',
            'options'=>array(
    //            'values'=>array((int)$model->minPriceRange,(int)$model->maxPriceRange),// default selection
    //            'values'=>array(2,5),// default selection
                'min'=>0, //minimum value for slider input
                'max'=>$maxPrice, // maximum value for slider input
                'animate'=>true,
                'range'=>true,
                'step'=>0.5,
                // on slider change event
                'slide'=>'js:function(event,ui){
                    $("#ticket-price-range").val(ui.values[0]+\'-\'+ui.values[1]);
                }',
            ),
            // slider css options
            'htmlOptions'=>array(
                'class'=>'input-medium'
            ),
        ));
        ?>

        <?php 
        $maxPrize = Lotteries::model()->getMaxPrizePrice()+0; 
        $model->minPrizePriceRange=$model->minPrizePriceRange+0; // Trick to format decimals
        $model->maxPrizePriceRange=$model->maxPrizePriceRange+0;
        if(!$model->minPrizePriceRange){
            $model->minPrizePriceRange=0;
        }
        if(!$model->maxPrizePriceRange){
            $model->maxPrizePriceRange=$maxPrize;
        }
        ?>
        <?php echo $form->labelEx($model,'prizePrice'); ?>
        <input type="text" class="input-medium" id="prize-price-range"  value="<?php echo $model->minPrizePriceRange . " - " . $model->maxPrizePriceRange;?>" style="border:0; color:#f6931f; font-weight:bold;" />
        <?php
        $this->widget('zii.widgets.jui.CJuiSliderInput', array(
            'name'=>'prize_price_slider',    
            'event'=>'change',
            'model'=>$model,
            'attribute'=>'minPrizePriceRange',
            'maxAttribute'=>'maxPrizePriceRange',
            'options'=>array(
    //            'values'=>array((int)$model->minPriceRange,(int)$model->maxPriceRange),// default selection
    //            'values'=>array(2,5),// default selection
                'min'=>0, //minimum value for slider input
                'max'=>$maxPrize, // maximum value for slider input
                'animate'=>true,
                'range'=>true,
                'step'=>  round(($model->minPrizePriceRange - $model->maxPrizePriceRange) / 10),
                // on slider change event
                'slide'=>'js:function(event,ui){
                    $("#prize-price-range").val(ui.values[0]+\'-\'+ui.values[1]);
                }',
            ),
            // slider css options
            'htmlOptions'=>array(
                'class'=>'input-medium'
            ),
        ));
        ?>
        <?php echo $form->labelEx($model,'geo'); ?>
        <?php 
        /* http://www.yiiframework.com/extension/egmap/ */
        $this->widget('gmap.EGMapAutocomplete', array(
            'name' => 'city',
            'model' => $model,
            'value' => $model->geo,
            'attribute' => 'geo',
            'htmlOptions'=>array(
                'class'=>'input-medium'
            ),
            'options' => array(
               'types' => array(
                 '(cities)'
               ),
               /*'componentRestrictions' => array(
                  'country' => 'us',
                )*/
            )
        ));
        ?>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Search', array('name' => 'search', 'class' => 'btn')); ?>
            <?php echo CHtml::submitButton('Reset', array('name' => 'reset', 'class' => 'btn')); ?>
        </div>        
    <?php } ?>
    <?php $this->endWidget(); ?>
</div>