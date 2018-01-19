<?php

use yii\helpers\Html; ?>

<div class="modal fade" id="modalPrinter" tabindex="-1" role="dialog">
    <div class="modal-dialog">                        
        <div class="box box-solid box-success">
            <div class="box-header">
                <h3 class="box-title">Printer</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">

                    <?php foreach ($modelPrinter as $dataPrinter): ?>

                        <div class="col-sm-4">
                            <?= Html::checkbox($dataPrinter['printer'], false, ['id' => $dataPrinter['printer'], 'class' => 'printerName', 'value' => $dataPrinter['printer']]) ?>
                            <?= Html::hiddenInput('printer', $dataPrinter['is_autocut'], ['id' => 'printer', 'class' => $dataPrinter['printer']]) ?>
                            &nbsp;&nbsp;
                            <label class="control-label" for="<?= $dataPrinter['printer'] ?>"><?= $dataPrinter['printer'] ?></label>
                        </div>

                        <?php endforeach; ?>

                </div>
            </div><!-- /.box-body -->
            <div class="box-footer" style="text-align: right">
                <button id="submitPrint" type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i>&nbsp;&nbsp;&nbsp;Cancel</button>
            </div>
        </div><!-- /.box -->
    </div>    
</div>

<div class="modal fade" id="modalLoading" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">                        
        <div class="box box-solid box-success">
            <div class="box-header">
                <h3 class="box-title">Printing In Progress........</h3>                
            </div>
            <div class="box-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                      Please wait.....
                    </div>
                </div>
            </div><!-- /.box-body -->            
        </div><!-- /.box -->
    </div>    
</div>

<div class="modal fade" id="modalAlert" tabindex="-1" role="dialog">
    <div class="modal-dialog">                        
        <div class="box box-solid box-danger">
            <div class="box-header">
                <h3 class="box-title">Printing Error</h3>                
            </div>
            <div class="box-body">
                <div id="modalAlertBody"></div>
            </div><!-- /.box-body -->            
        </div><!-- /.box -->
    </div>    
</div>

<?php
foreach ($modelPrinterKasir as $dataPrinterKasir) {
    echo Html::hiddenInput('printerKasir', $dataPrinterKasir['printer'], ['id' => 'printerKasir']);
    echo Html::hiddenInput('printer', $dataPrinterKasir['is_autocut'], ['id' => 'printer', 'class' => $dataPrinterKasir['printer']]);
} ?>

