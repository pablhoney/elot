<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="">
    <div class="panel panel-default bootstrap-widget-table">
        <div class="panel-body">
            <table class="table table-hover">

            <?php 
              $this->widget('zii.widgets.CListView', array(
                    'dataProvider'=>$dataProvider,
                    'itemView'=>'notifyRow',   // refers to the partial view named '_post'
                    'enableSorting'=>false,
              ));
                    
            ?>
            
            </table>
        </div>
    </div>
</div>