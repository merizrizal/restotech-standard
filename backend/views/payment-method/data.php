<?php

use yii\helpers\Html;
use restotech\standard\backend\components\GridView;
use restotech\standard\backend\components\NotificationDialog;

$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null) : 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();

endif;

$this->title = 'Metode Pembayaran';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="payment-method-init">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'bordered' => false,
        'floatHeader' => true,
        'panel' => [
            'heading' => '',
        ],
        'toolbar' => null,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'nama_payment',
            'type',
            'method',
            'keterangan:ntext',
        ],
        'tableOptions' => [
            'class' => 'table table-striped table-hover'
        ],
    ]); ?>
</div>