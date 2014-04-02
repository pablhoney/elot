<?php
class CronTicketsCommand extends CConsoleCommand
{
   
    public function run($args)
    {

        $busy = file_exists(Yii::app()->basePath."/cron-ticket.lock");
        if(!$busy){
            Yii::log("CRON OK!", "warning");
            $file=Yii::app()->basePath."/cron-ticket.lock";
            $oFile=fopen($file,"w");
            fwrite($oFile,"DO");
            fclose($oFile);
            try {
                // ADD TO CRONTAB: php /path/to/cron.php CronTickets
                Yii::log("CRON Tickets OK!", "warning");
                $errors = array('tickets'=>array());

                Lotteries::model()->sendTicketsEmail($errors);
                Yii::log("CRON Tickets Fine", "warning");

                if(count($errors['tickets']) > 0){
                    $emailRes=EmailManager::sendCronAdminEmail($errors);
                }
            } catch (Exception $exc) {
                Yii::log("CRON Tickets error:".$exc->getTraceAsString(),'error');
            }
            unlink($file);
            } else {
            Yii::log("CRON Tickets Busy -> SKIP ", "warning");
        }
    }

}    