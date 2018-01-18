<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Item */

$this->title = 'Create Item';
$this->params['breadcrumbs'][] = ['label' => 'Item', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-create">

    <?= $this->render('_form', [
        'model' => $model,
        'modelSkus' => $modelSkus,
    ]) ?>

</div>
