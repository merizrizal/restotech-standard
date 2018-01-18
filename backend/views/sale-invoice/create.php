<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\SaleInvoice */

$this->title = 'Create Sale Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Sale Invoice', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-invoice-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
