
<h2>Distribuzione Random Generata con valori: K=<?php echo $randValues['k']; ?> e Lambda=<?php echo $randValues['lambda']; ?></h2>
<?php
/* @var $this LotteriesController */
/* @var $model Lotteries */

foreach($random as $k=>$rand){
    echo '<p>Valori fino a K='.$k.' - ';
    echo $rand;
    echo '</p>';
}

?>