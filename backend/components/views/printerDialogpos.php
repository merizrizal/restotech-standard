<?php

use yii\helpers\Html; ?>

<div class="modal fade" id="modalPrinter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Printer</h4>
            </div>
            <div class="modal-body">                
                <div class="row">
                    
                    <?php
                    foreach ($modelPrinter as $dataPrinter): ?>
                        
                        <div class="col-sm-4">
                            <?= Html::checkbox($dataPrinter['printer'], false, ['id' => $dataPrinter['printer'], 'class' => 'printerName', 'value' => $dataPrinter['printer']]) ?>
                            <?= Html::hiddenInput('printer', $dataPrinter['is_autocut'], ['id' => 'printer', 'class' => $dataPrinter['printer']]) ?>
                            &nbsp;&nbsp;
                            <label class="control-label" for="<?= $dataPrinter['printer'] ?>"><?= $dataPrinter['printer'] ?></label>
                        </div>

                    <?php
                    endforeach; ?>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> &nbsp; Close</button>
                <button id="submitPrint" type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-check"></i> &nbsp;Print</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAlert" tabindex="-1" role="dialog">
    <div class="modal-dialog">                        
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h3 class="modal-title">Printing Error</h3>                
            </div>
            <div class="modal-body">
                <div id="modalAlertBody"></div>
            </div><!-- /.box-body -->            
        </div><!-- /.box -->
    </div>    
</div>

<div class="modal fade" id="modalLoading" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">                
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title">Printing In Progress........</h4>                
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                      Please wait.....
                    </div>
                </div>
            </div><!-- /.box-body -->            
        </div><!-- /.box -->
    </div>    
</div>

<?php
foreach ($modelPrinterKasir as $dataPrinterKasir) {
    echo Html::hiddenInput('printerKasir', $dataPrinterKasir['printer'], ['id' => 'printerKasir']);
    echo Html::hiddenInput('printer', $dataPrinterKasir['is_autocut'], ['id' => 'printer', 'class' => $dataPrinterKasir['printer']]);
} ?>