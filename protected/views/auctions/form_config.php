<?php
$buttons = array(
        'save' => array(
                'type' => 'submit',
                'label' => 'Save',
//                'onclick'=>'var sub=1;document.forms["lot-form"].submit();',
        ),
);
if($this->model->isNewRecord || $this->model->status==Yii::app()->params['lotteryStatusConst']['draft']){
    $buttons=array(
        'draft' => array(
                'type' => 'submit',
                'label' => 'Save as draft',
                'class' => 'btn btn-success',
        ),
        'publish' => array(
                'type' => 'submit',
                'label' => 'Publish',
                'class' => 'btn btn-success',
        ),
    );
}

return array(
	'title' => Yii::t('clos', 'Auctions'),
	'attributes' => array(
		'enctype' => 'multipart/form-data',
                'id' => 'lot-form',
//                'onsubmit' => "if(typeof(sub) == 'undefined'){return false;}"
	),    
        'elements' => array(
            'name' => array(
                    'label' => Yii::t('clos', 'Lottery Name'),
                    'type' => 'text',
                    'size'=>25,
                    'maxlength'=>45
            ),
      
            'prize_desc' => array(
                        'label' => Yii::t('clos', 'Prize Description'),
                        'type' => 'ext.imperavi-redactor-widget.ImperaviRedactorWidget',
                        'options' => array(
                            'buttons' => array('html', 'formatting', // togliere per PRODUZIONE
                                'bold', 'italic', 'deleted', 'unorderedlist', 
                                'orderedlist', 'outdent', 'indent','table', 
                                'alignment', 'horizontalrule')
                        ),
            ),
            'prize_category' => array(
                        'label' => 'Prize Category',//Yii::t('clos', 'Section'),
                        'type' => 'dropdownlist',
                        'items' => CHtml::listData(PrizeCategories::model()->getPrizeCatList(), 'id', 'category_name')
            ),
            'prize_conditions' => array(
                    'label' => Yii::t('clos', 'Prize Conditions'),
                    'type' => 'dropdownlist',
                    'items' => CHtml::listData(Yii::app()->params['prizeConditions'], 'id', 'name')
            ),
            'prize_condition_text' => array(
                    'label' => Yii::t('clos', 'Prize Condition Text'),
                    'type' => 'text',
                    'size'=>45,
                    'maxlength'=>45
            ),
            'prize_shipping' => array(
                    'label' => 'Prize Shipping',//Yii::t('clos', 'Section'),
                    'type' => 'dropdownlist',
                    'items' => CHtml::listData(Yii::app()->params['speditionType'], 'id', 'type')
            ),
            'prize_price' => array(
                        'label' => Yii::t('clos', 'Prize Value'),
                        'type' => 'ext.prizeCalculator.PrizeCalculatorWidget'
            ),
            'ticket_value' => array(
                    'label' => Yii::t('clos', 'ticket_value'),
                    'type' => 'text'
            ),
            'lottery_start_date' => array(
                    'id' => 'lot_start',
                    'label' => 'lottery_start_date start',
                    'type' => 'zii.widgets.jui.CJuiDatePicker',
                    'options'=>array(
                            'dateFormat'=>'dd/mm/yy',
                            'minDate'=>date('d/m/Y'),
                            //'dateFormat'=>'yy-mm-dd',
                            'timeFormat'=>'HH:mm:ss',
                            'showSecond'=>true,
                            'showTimezone'=>false,
                            'language' => 'it',
                            'ampm' => false,
                            'showAnim'=>'fold',
                            'onSelect'=>'js:function(selDate,obj){
                                $("#lot_end").datepicker("option","minDate",selDate);
                            }',
                    ),
                    'language' => 'it',
                    'htmlOptions'=>array(
                        
                    ),
            ),
            'lottery_draw_date' => array(
                    'id' => 'lot_end',
                    'label' => 'lottery_draw_date start',
                    'type' => 'zii.widgets.jui.CJuiDatePicker',
                    'options'=>array(
                            'dateFormat'=>'dd/mm/yy',
                            'minDate'=>date('d/m/Y'),
                            //'dateFormat'=>'yy-mm-dd',
                            'timeFormat'=>'HH:mm:ss',
                            'showSecond'=>true,
                            'showTimezone'=>false,
                            'language' => 'it',
                            'ampm' => false,
                            'showAnim'=>'fold',
                            'onSelect'=>'js:function(selDate,obj){
                                $("#lot_start").datepicker("option","maxDate",selDate);
                            }',
                    ),
                    'language' => 'it',
                    'htmlOptions'=>array(
                        
                    ),
            ),
            
	),
	'buttons' => $buttons,
);