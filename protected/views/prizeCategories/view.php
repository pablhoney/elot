
<h1>Asta</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'/auctions/_view',
)); ?>
