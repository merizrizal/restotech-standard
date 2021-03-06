<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\money\MaskMoney;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\Employee */
/* @var $form yii\widgets\ActiveForm */

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

endif; ?>

<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="box box-danger">
            <div class="box-body">
                <div class="employee-form">                
                    <?php $form = ActiveForm::begin([
                            'options' => [
                                'enctype' => 'multipart/form-data'
                            ],                            
                            'fieldConfig' => [
                                'parts' => [
                                    '{inputClass}' => 'col-lg-12'
                                ],
                                'template' => '<div class="row">'
                                                . '<div class="col-lg-3">'
                                                    . '{label}'
                                                . '</div>'
                                                . '<div class="col-lg-6">'
                                                    . '<div class="{inputClass}">'
                                                        . '{input}'
                                                    . '</div>'
                                                . '</div>'
                                                . '<div class="col-lg-3">'
                                                    . '{error}'
                                                . '</div>'
                                            . '</div>', 
                            ]
                    ]); ?>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!$model->isNewRecord)
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create'], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>                    
                    
                    <?php
                    if (!$model->isNewRecord) {
                        echo $form->field($model, 'kd_karyawan', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ],
                        ])->textInput(['maxlength' => true, 'readonly' => 'readonly']);
                    } ?>

                    <?= $form->field($model, 'password_absen')->textInput(['maxlength' => 32]) ?>
                    
                    <?= $form->field($model, 'nama')->textInput(['maxlength' => 64]) ?>

                    <?= $form->field($model, 'alamat')->textarea(['rows' => 6]) ?>

                    <?= $form->field($model, 'jenis_kelamin')->radioList(['Pria' => 'Pria', 'Wanita' => 'Wanita'], ['separator' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;']) ?>

                    <?= $form->field($model, 'phone1', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>

                    <?= $form->field($model, 'phone2', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->textInput(['maxlength' => 15]) ?>                    

                    <?= $form->field($model, 'limit_officer', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->widget(MaskMoney::className()) ?>

                    <?= $form->field($model, 'sisa', [
                            'parts' => [
                                '{inputClass}' => 'col-lg-7'
                            ]
                        ])->widget(MaskMoney::className(), ['readonly' => 'readonly']) ?>

                    <?= $form->field($model, 'not_active')->checkbox(['value' => true], false) ?>
                    
                    <?= $form->field($model, 'image')->widget(FileInput::classname(), [
                            'options' => [
                                'accept' => 'image/*'
                            ],
                            'pluginOptions' => [
                                'initialPreview' => [
                                    Html::img(Yii::getAlias('@uploadsUrl') . $model->thumb('/img/employee/', 'image', 200, 200), ['class'=>'file-preview-image']),
                                ],
                                'showRemove' => false,
                                'showUpload' => false,
                            ]
                        ]); ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <?php
                                $icon = '<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;';
                                echo Html::submitButton($model->isNewRecord ? $icon . 'Save' : $icon . 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                                echo '&nbsp;&nbsp;&nbsp;';                                
                                echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['index'], ['class' => 'btn btn-default']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php                    
                    ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2"></div>
</div><!-- /.row -->

<?php 

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
   
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("#employee-sisa-disp").off("keypress");
    $("#employee-sisa-disp").off("keyup");
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>