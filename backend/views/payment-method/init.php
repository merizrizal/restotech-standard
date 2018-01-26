<?php

use yii\helpers\Html;

$this->title = 'Inisialisasi Metode Pembayaran';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="payment-method-init">
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="box box-danger">
                <div class="box-body">
                    <div class="payment-method-form">
                        <br><br>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    <?= Html::a('<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;' . 'Inisialisasi', ['init'], ['class' => 'btn btn-primary btn-lg', 'data-method' => 'post']); ?>
                                </div>
                            </div>
                        </div>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>