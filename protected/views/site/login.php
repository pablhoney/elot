
      <div class="modal-body" id='loginFormBody'>
        <?php if($authenticated) { ?>
            <script type="text/javascript">
                window.top.location = "<?php echo Yii::app()->getBaseUrl(true).$this->createUrl($model->originUrl); ?>";
            </script>
        <?php } ?>
        <?php
        $model = isset($model) ? $model : new LoginForm;
        if($this->getAction()->getId() !== "error"){
            $model->originUrl = $this->getId() . '/' . $this->getAction()->getId();
        }
        ?>
        <div class="form">
        <?php $form=$this->beginWidget('CActiveForm',array(
            'id'=>'user_login_form',
            'action' => $this->createUrl('site/login'),
            'htmlOptions' => array('class' => 'well form-horizontal','enctype' => 'multipart/form-data', 'role' => 'form'), // for inset effect
            )); ?>

            <?php echo $form->errorSummary($model); ?>

            <div class="row">
                <div class="form-group">
                    <?php echo $form->textField($model,'username',array('placeholder' => 'email','class'=>' form-control')); ?>
                    <?php echo $form->error($model,'username'); ?>
                </div>
                <div class="form-group">
                    <?php echo $form->passwordField($model,'password',array('placeholder' => 'password','class'=>' form-control')); ?>
                    <?php echo $form->error($model,'password'); ?>
                </div>
                <div class="checkbox">
                    <?php echo $form->checkBox($model,'rememberMe'); ?>
                    <?php echo $form->label($model,'rememberMe'); ?>
                    <?php echo $form->error($model,'rememberMe'); ?>
                    <?php echo $form->hiddenField($model,'originUrl'); ?>
                </div>
            </div>

            <div class="row buttons">
                    <?php echo CHtml::ajaxSubmitButton(
                            Yii::t('app', 'Submit'), 
                            array('site/login'), 
                            array('update'=>'#loginFormBody'), 
                            array("class"=>"btn btn-primary btn-large")
                    );
    //                echo CHtml::Button(Yii::t('app', 'Submit') ,array('onclick'=>'send();'));
                    ?>
            </div>

        <?php $this->endWidget(); ?>
        </div><!-- form -->
        <div class="">
            <?php $this->widget('ext.hoauth.widgets.HOAuth'); ?>
        </div>
      </div>