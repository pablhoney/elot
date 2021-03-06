<div class="panel panel-default bootstrap-widget-table">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">
                <?php
                   //disabled="disabled"
                   $htmlDisabled=array('class'=>'form-control', 'autocomplete'=>'off');
                   if($model->id){
                        echo Yii::t('wonlot','Modifica asta') . " " .$model->name;
                        if($model->status >= 3){
                            $htmlDisabled = array_merge($htmlDisabled,array("disabled"=>"disabled"));
                        }
                   } else {
                        echo Yii::t('wonlot','Crea asta');
                   }
                ?>
            </h3>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#help-modal"><i class="glyphicon glyphicon-question-sign"></i></button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <div class=".container-fluid">
            <?php $form = $this->beginWidget('CActiveForm', array(
                        'id'=>'lot-form',
                        'enableAjaxValidation'=>false,
                    ), array(
                        'class'=>'form-inline',
                )); ?>
            <div class="col-md-12">
                <!--SAVED IMG SECTION-->
                <?php echo $form->errorSummary($model); ?>
                <?php if(!$model->isNewRecord){ ?>
                    <div class="form-group">
                        La Asta è in stato: <?php echo $model->getStatusText(); ?>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'name'); ?>
                    <?php echo $form->textField($model,'name',array('size'=>25,'maxlength'=>45,'class'=>'form-control')); ?>
                    <?php echo $form->error($model,'name'); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'prize_desc'); ?>
                    <?php 
                        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
                            'model' => $model,
                            'attribute' => 'prize_desc',
                            'options' => array(
                               'buttons' => array('html', 'formatting', // togliere per PRODUZIONE
                                    'bold', 'italic', 'deleted', 'unorderedlist', 
                                    'orderedlist', 'outdent', 'indent','table', 
                                    'alignment', 'horizontalrule'),
                                'minHeight' => 200,
                            ),
                            'htmlOptions' => array('class'=>'form-control')
                        ));
                        ?>
                </div>
            </div>
            <div class="col-md-6">                
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'prize_category'); ?>
                        <?php echo $form->dropDownList($model,'prize_category',CHtml::listData(PrizeCategories::model()->getPrizeCatList(), 'id', 'category_name'),$htmlDisabled); ?>
                        <?php echo $form->error($model,'prize_category'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'prize_conditions'); ?>
                        <?php echo $form->dropDownList($model,'prize_conditions',CHtml::listData(Yii::app()->params['prizeConditions'], 'id', 'name'),$htmlDisabled); ?>
                        <?php echo $form->error($model,'prize_conditions'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'prize_condition_text'); ?>
                        <?php echo $form->textField($model,'prize_condition_text',array('size'=>25,'maxlength'=>45,'class'=>'form-control')); ?>
                        <?php echo $form->error($model,'prize_condition_text'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model,'prize_shipping'); ?>
                        <?php echo $form->dropDownList($model,'prize_shipping',CHtml::listData(Yii::app()->params['speditionType'], 'id', 'type'),$htmlDisabled); ?>
                        <?php echo $form->error($model,'prize_shipping'); ?>
                    </div>
                    <!--<div class="form-group">-->
                        <?php //echo $form->labelEx($model,'prize_price'); ?>
                        <?php 
                            /*$this->widget('ext.prizeCalculator.PrizeCalculatorWidget', array(
                                'model' => $model,
                                'attribute' => 'prize_price',
                                'htmlOptions' => $htmlDisabled,
                            ));*/
                            ?>
                    <!--</div>-->
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php $htmlTicketValue = array_merge($htmlDisabled,array("step"=>"0.01","min"=>0.01)); ?>
                    <?php echo $form->labelEx($model,'ticket_value'); ?>
                    <?php echo $form->numberField($model,'ticket_value',$htmlTicketValue); ?>
                    <?php echo $form->error($model,'ticket_value'); ?>
                </div>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'lottery_start_date'); ?>
                    <?php echo 
//                            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        $this->widget('ext.EJuiDateTimePicker.EJuiDateTimePicker', array(
                            'id' => 'lot_start',
                            'model' => $model,
                            'attribute' => 'lottery_start_date',
                            'htmlOptions' => $htmlDisabled,
                            'options'=>array(
                                    'dateFormat'=>Yii::app()->params['toUserDateFormat'],
                                    'minDate'=>date('d/m/Y'),
                                    'timeFormat'=>Yii::app()->params['toUserTimeFormat'],
                                    'showSecond'=>false,
                                    'showTimezone'=>false,
                                    'language' => 'it',
                                    'ampm' => false,
                                    'showAnim'=>'fold',
                                    'onSelect'=>'js:function(selDate,obj){
                                        $.checkMaxDate(selDate);
                                    }',
                            ),
                            'language' => 'it',
                        ),true);
                    ?>
                    <?php echo $form->error($model,'lottery_start_date'); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'lottery_draw_date'); ?>
                    <?php echo 
//                            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        $this->widget('ext.EJuiDateTimePicker.EJuiDateTimePicker', array(
                            'id' => 'lot_end',
                            'model' => $model,
                            'attribute' => 'lottery_draw_date',
                            'htmlOptions' => $htmlDisabled,
                            'options'=>array(
                                    'dateFormat'=>Yii::app()->params['toUserDateFormat'],
                                    'minDate'=>date('d/m/Y'),
                                    'timeFormat'=>Yii::app()->params['toUserTimeFormat'],
                                    'showSecond'=>false,
                                    'showTimezone'=>false,
                                    'language' => 'it',
                                    'ampm' => false,
                                    'showAnim'=>'fold',
                                    'onSelect'=>'js:function(selDate,obj){
                                        $.checkMinDate(selDate);
                                    }',
                            ),
                            'language' => 'it',
                        ),true);
                    ?>
                    <?php echo $form->error($model,'lottery_draw_date'); ?>
                </div>
                <?php echo $form->labelEx($model,'lot_location'); ?>
                <?php 
                /* http://www.yiiframework.com/extension/egmap/ */
                $this->widget('gmap.EGMapAutocomplete', array(
                    'name' => 'lot_location',
                    'model' => $this->locationForm,
                    'attribute' => 'address',
                    'htmlOptions' => $htmlDisabled,
                    /*'options' => array(
                       'types' => array(
                         '(geocode)'
                       ),
                    )*/
                ));
                ?>
                <div class="">
                    <?php if(!empty($model->id) || $model->cloneId){ ?>
                      <?php $this->renderPartial('_setDefaultImage',array('data'=>$model)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-12">
                <?php
                if($model->isNewRecord){
                    echo CHtml::submitButton('Salva come bozza',array('name'=>'save','class'=>'btn btn-primary lot-sub-btn'));
                    echo CHtml::submitButton('Pubblica',array('name'=>'publish','class'=>'btn btn-success lot-sub-btn'));
                } else {
                    if($model->status==Yii::app()->params['lotteryStatusConst']['draft']){
                        echo CHtml::submitButton('Salva come bozza',array('name'=>'save','class'=>'btn btn-primary lot-sub-btn'));
                        echo CHtml::submitButton('Pubblica',array('name'=>'publish','class'=>'btn btn-success lot-sub-btn'));
                    } else {
                        echo CHtml::submitButton('Salva',array('name'=>'save','class'=>'btn btn-primary lot-sub-btn'));
                        echo CHtml::link('Indietro', CController::createUrl('auctions/'.$model->id),array('class'=>'btn btn-default'));
                    }
                }
                ?>
                <div id="prize_img">
                    <script>var imgCount=0;</script>
                <?php
                    //echo $form->labelEx($model,'photos');

                    $this->widget( 'xupload.XUpload', array(
                        'url' => Yii::app( )->createUrl( "/auctions/upload"),
                        'model' => $this->upForm,
                        'htmlOptions' => array('id'=>'lot-form'),
                        'attribute' => 'file',
                        'multiple' => true,
                        'showForm' => false,
                        'entityModel' => $model,
                        'options'=>array(
                            'added' => 'js:function(e, data) { '
                                    . "if(imgCount == 0){"
                                        . "$('.lot-sub-btn').attr('disabled','disabled');"
                                    ."}"
                                    . "imgCount = imgCount + 1;"
                                    ."}",
                            'completed' => 'js:function(e, data) { '
                                    . "imgCount = imgCount - 1;"
                                    . "if(imgCount == 0){"
                                        . "$('.lot-sub-btn').attr('disabled',false);"
                                    ."}"
                                    ."}",
                            'failed' => 'js:function(e, data) { '
                                    . "imgCount = imgCount - 1;"
                                    . "if(imgCount == 0){"
                                        . "$('.lot-sub-btn').attr('disabled',false);"
                                    ."}"
                                    ."}",
                        ),
                    ));
                ?>
                </div>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>

<?php $this->renderPartial('_helpModal'); ?>
