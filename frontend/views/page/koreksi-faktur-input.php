<?php
use yii\helpers\Html; 
use backend\components\VirtualKeyboard; ?>


<div class="col-lg-12">

    <div class="row mt">
        <div class="col-md-12 col-sm-12">
            <div class="weather-2 pn" style="height: auto; padding: 0 0 20px 0">
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">                            
                            <p>
                                &nbsp;
                            </p>
                        </div>
                    </div>                    
                </div>
                
                
                <div class="row data mt">
                    <?= Html::beginForm(Yii::$app->urlManager->createUrl('page/koreksi-faktur'), 'get', ['id' => 'koreksiFaktur']); ?>
                    
                    <div class="col-lg-3"></div> 
                    
                    <div class="col-lg-6">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3">
                                    <label class="control-label" for="invoice-id">No. Faktur</label>
                                </div>
                                <div class="col-lg-6">
                                    <div class="col-lg-12">
                                        <?= Html::textInput('id', null, ['class' => 'form-control', 'id' => 'invoiceId']) ?>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="help-block"></div>                                        
                                </div>                                    
                            </div>
                        </div>   
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <?php
                                    echo Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;&nbsp;&nbsp;Submit', ['class' => 'btn btn-success']);
                                    echo '&nbsp;&nbsp;&nbsp;';
                                    echo Html::a('<i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;Cancel', ['page/index'], ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?= Html::endForm(); ?>
                    
                    <div class="col-lg-3"></div>                                        
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$virtualKeyboard = new VirtualKeyboard();
$virtualKeyboard->registerCssFile();
$virtualKeyboard->registerJsFile();

$jscript = $virtualKeyboard->keyboardQwerty('#invoiceId');

$this->registerJs($jscript); ?>