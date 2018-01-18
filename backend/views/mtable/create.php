<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Mtable */

$this->title = 'Create Meja';
$this->params['breadcrumbs'][] = ['label' => 'Meja Ruangan ' . $model->mtableCategory->nama_category, 'url' => ['index', 'cid' => $model->mtable_category_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mtable-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
