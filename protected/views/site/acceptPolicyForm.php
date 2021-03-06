<?php if($model->complete){ ?>
    <h3><?php echo Yii::t("wonlot","Grazie per aver accettato i Termini e Condizioni..."); ?></h3>
    <script>
        $(".form-group").hide();
        setTimeout(function(){ 
            window.location.href = "/";
        }, 3000);
    </script>
<?php } else { ?>    
    <?php $form=$this->beginWidget('CActiveForm',array(
            'id'=>'user_accept_form',
            'htmlOptions' => array('enctype' => 'multipart/form-data', 'role' => 'form'), // for inset effect
        )); ?>
            <div class="row rememberMe checkbox-container">
                <div class="checkbox">
                    <?php echo $form->checkBox($model,'terms'); ?>
                    <?php echo $form->label($model,'Accetti i termini e condizioni?',array('class'=>' ')); ?>
                    <?php echo $form->error($model,'terms'); ?>
                </div>
            </div>
            <div class="row rememberMe checkbox-container">
                <div class="checkbox">
                    <?php echo $form->checkBox($model,'persdatamng',array('class'=>' ')); ?>
                    <?php echo $form->label($model,'Accetti il termini di gestione dei dati personali?',array('class'=>'')); ?>
                    <?php echo $form->error($model,'persdatamng'); ?>
                </div>
            </div>
            <div class="row rememberMe checkbox-container">
                <div class="checkbox">
                    <?php echo $form->checkBox($model,'newsletterAccept',array('class'=>' ')); ?>
                    <?php echo $form->label($model,'Accetti di ricevere newsletter promozionali sulle aste in corso?',array('class'=>'')); ?>
                    <?php echo $form->error($model,'newsletterAccept'); ?>
                </div>
            </div>
    <?php $this->endWidget(); ?>

<?php } ?>
