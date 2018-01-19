<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use restotech\standard\backend\models\UserAppModule;
use restotech\standard\backend\components\NotificationDialog;

/* @var $this yii\web\View */
/* @var $model restotech\standard\backend\models\UserLevel */
/* @var $form yii\widgets\ActiveForm */

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeAsset::register($this);

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

<?php $form = ActiveForm::begin([
            'options' => [

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

<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="box box-danger">
            <div class="box-body">
                <div class="user-level-form">                    
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php
                                if (!$model->isNewRecord)
                                    echo Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create', ['create'], ['class' => 'btn btn-success']); ?>
                            </div>
                        </div>
                    </div>

                    <?= $form->field($model, 'nama_level')->textInput(['maxlength' => 32]) ?>
                    
                    <?= $form->field($model, 'is_super_admin')->checkbox(['value' => true], false) ?>
                    
                    <?= $form->field($model, 'default_action')->dropDownList(
                            ArrayHelper::map(
                                UserAppModule::find()->/*limit(30)->*/all(), 
                                'id', 
                                function($data) {
                                    if ($data->sub_program == '/') {
                                        return '(frontend)/' . $data->nama_module . '/' . $data->module_action;    
                                    } else {
                                        return $data->sub_program . '/' . $data->nama_module . '/' . $data->module_action;                                 
                                    }
                                }
                            ), 
                            [
                                'prompt' => '',
                            ]) ?>

                    <?= $form->field($model, 'keterangan')->textarea(['rows' => 2]) ?>

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

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2"></div>
</div><!-- /.row -->

<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">
                    Roles
                </h3>
                <div class="box-tools">
                    
                </div>
            </div>
            <div class="box-body">
                <div class="row" id="roles">
                    <?php                    
                    foreach ($modelUserAppModule as $keySubprogram => $subprogram):    
                        foreach ($subprogram as $key => $value): ?>

                            <div class="col-lg-4" id="roles-item">
                                <div class="box box-solid bg-green">
                                    <div class="box-header">
                                        <h3 class="box-title">
                                            
                                            <?php
                                            if ($value[0]['sub_program'] == '/') {
                                                echo '(frontend)/' . $key;
                                            } else {
                                                echo $value[0]['sub_program'] . '/' . $key;
                                            } ?>
                                            
                                        </h3>
                                    </div>
                                    <div class="box-body">
                                        <?php
                                        foreach ($value as $moduleAction) { 
                                            $checkBoxId = $keySubprogram . $moduleAction['nama_module'] . '-' . $moduleAction['module_action'];
                                            $checkBoxName = 'roles[' . $keySubprogram . $moduleAction['nama_module'] . $moduleAction['module_action'] . '][action]';
                                            $hiddenInputName = 'roles[' . $keySubprogram . $moduleAction['nama_module'] . $moduleAction['module_action'] . '][userAksesId]';
                                            $hiddenInputName2 = 'roles[' . $keySubprogram . $moduleAction['nama_module'] . $moduleAction['module_action'] . '][appModuleId]';
                                            $isActive = false;
                                            $userAksesId = 0;

                                            if (count($moduleAction['userAkses']) > 0) {
                                                $userAksesId = $moduleAction['userAkses'][0]['id'];
                                                $isActive = $moduleAction['userAkses'][0]['is_active'];
                                            }

                                            echo Html::hiddenInput($hiddenInputName, $userAksesId);
                                            echo Html::hiddenInput($hiddenInputName2, $moduleAction['id']);
                                            echo Html::checkbox($checkBoxName, $isActive, ['id' => $checkBoxId, 'value' => $moduleAction['id']]) . '&nbsp; &nbsp; ';
                                            echo Html::label($moduleAction['module_action'], $checkBoxId);
                                            echo '<br>';
                                        } ?>
                                    </div><!-- /.box-body -->
                                </div>
                            </div>                                        

                        <?php
                        endforeach; 
                    endforeach; ?>
                </div>
                
            </div>
        </div>
    </div>
    <div class="col-sm-1"></div>
</div>

<?php ActiveForm::end(); ?>

<?php

$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);
 
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/masonry/masonry.pkgd.min.js', ['depends' => 'yii\web\YiiAsset']);


$jscript = '
    $("#userlevel-default_action").select2({
        theme: "krajee",
        placeholder: "Pilih",
        allowClear: true
    });
    
    $("#roles").masonry({
        itemSelector: "#roles-item",
    });
';

$this->registerJs($jscript . Yii::$app->params['checkbox-radio-script']()); ?>