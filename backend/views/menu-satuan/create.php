<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\MenuSatuan */

$this->title = 'Create Satuan Menu';
$this->params['breadcrumbs'][] = ['label' => 'Satuan Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-satuan-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
