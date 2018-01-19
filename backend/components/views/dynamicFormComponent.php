<?php
use yii\helpers\Html;
use yii\helpers\Inflector;
use kartik\money\MaskMoney;
use yii\widgets\ActiveForm;

$assetCommon = $this->assetBundles['common\assets\AppAsset'];

$tableName = $model[0]->tableName(); ?>

<table id="table-<?= $tableName ?>" style="display: none">
    <tr>

        <?php
        foreach ($tableFields as $field => $tableField): ?>
       
            <td>
                <span id="data-<?= $tableName . '-' . str_replace('.', '_', $field) ?>"></span>
            </td>
        
        <?php
        endforeach; ?>
        
        <td>
            <div id="groupButtonAction" class="btn-group btn-group-xs" role="group" style="width: 75px">
                <a data-original-title="Edit" id="aEdit-<?= $tableName ?>" class="btn btn-success" href="/privatebdg/administrator/guru/update/1" title="" data-toggle="tooltip" data-placement="top"><i class="glyphicon glyphicon-pencil"></i></a>
                <a data-original-title="Delete" id="aDelete-<?= $tableName ?>" class="btn btn-danger" href="/privatebdg/administrator/guru/delete/1" title="" data-toggle="tooltip" data-placement="top"><i class="glyphicon glyphicon-trash"></i></a>
            </div>
        </td>
    </tr>
</table>

<div class="modal fade" id="modalDialog-<?= $tableName ?>" role="dialog">
    <div class="modal-dialog">    
        <div class="modal-content" id="<?= ($type == 'frontend') ? 'product-details-modal' : '' ?>">
            
            <?php
            if ($type == 'frontend'): ?>
            
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button"> ×</button>
            
            <?php
            endif; ?>
            
            <div class="col-md-12" style="padding: 30px">
                <?php $form = ActiveForm::begin([
                            'id' => 'form-' . $tableName,
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
                    ]); 

                    echo Html::hiddenInput('inputState', null, ['id' => 'inputState']);
                    echo Html::hiddenInput('currentIndexTrx', null, ['id' => 'currentIndexTrx']); ?>                   

                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">
                                Add <?= $title ?>
                            </h3>
                            <div class="box-tools pull-right">
                                
                                <?php
                                if ($type == 'backend'): ?>
                                
                                    <button class="btn btn-primary btn-sm" data-dismiss="modal">
                                        <i class="fa fa-times"></i>
                                    </button>
                                
                                <?php
                                endif; ?>
                                
                            </div>
                        </div>
                        <div class="box-body" id="body<?= $tableName ?>">                
                            <?php 
                            $jscriptAdding = '';
                            $jscriptAddFunction = '';
                            $jscriptEditFunction = '';

                            foreach ($formFields as $field => $formField) {

                                $inputValue = 'thisObj.find("input.' . $tableName . '-' . $field . '").val()';

                                if ($formField['type'] == 'textinput') {

                                    echo $form->field($model[0], '[]' . $field, !empty($formField['attributeOptions']) ? $formField['attributeOptions'] : [])->textInput();

                                    $jscriptAddFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val("");';                        

                                    $jscriptEditFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val(' . $inputValue . ');';

                                } else if ($formField['type'] == 'textarea') {

                                    echo $form->field($model[0], '[]' . $field, !empty($formField['attributeOptions']) ? $formField['attributeOptions'] : [])->textarea(['rows' => 3]);

                                    $jscriptAddFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val("");';                        

                                    $jscriptEditFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val(' . $inputValue . ');';

                                } else if ($formField['type'] == 'dropdown') {

                                    echo $form->field($model[0], '[]' . $field, !empty($formField['attributeOptions']) ? $formField['attributeOptions'] : [])->dropDownList(
                                            $formField['data'], 
                                            [
                                                'style' => 'width: 100%'
                                            ]);

                                    $jscriptAddFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val(null).trigger("change");';

                                    $jscriptEditFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val(' . $inputValue . ').trigger("change");';

                                    if (empty($formField['noAdding'])) {
                                        $jscriptAdding .= '
                                            $("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").select2({
                                                placeholder: "Pilih",
                                                allowClear: true,
                                            });
                                        ';
                                    }
                                } else if ($formField['type'] == 'money') {
                                    echo $form->field($model[0], '[]' . $field, !empty($formField['attributeOptions']) ? $formField['attributeOptions'] : [])->widget(MaskMoney::className());
                                    
                                    $jscriptAddFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val("");';                        
                                    $jscriptAddFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '-disp").maskMoney("mask", 0);';                        

                                    $jscriptEditFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val(' . $inputValue . ');';
                                    $jscriptEditFunction .= '$("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '-disp").maskMoney("mask", parseFloat(' . $inputValue . '));';
                                }
                            } ?>
                        </div>
                        <div class="box-footer" style="text-align: right">
                            <?= Html::submitButton('<i class="fa fa-check"></i>&nbsp;&nbsp;&nbsp;Add', ['id' => 'aYes', 'class' => 'btn btn-primary']); ?>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <i class="fa fa-rotate-left"></i>&nbsp;&nbsp;&nbsp;
                                Cancel
                            </button>
                        </div> 
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>    
</div>


<?php

$this->registerCssFile($assetCommon->baseUrl . '/plugins/select2/select2.min.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($assetCommon->baseUrl . '/plugins/select2/select2.full.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '
    $("a#addButton-' . $tableName . '").click(function(event){
        event.preventDefault();' .
        
        $jscriptAddFunction . '
            
        if ($("#form-' . $tableName . ' .form-group").hasClass("has-error")) {
            $("#form-' . $tableName . ' .form-group").removeClass("has-error");
            $("#form-' . $tableName . ' .help-block").empty();
        }
        
        if ($("#form-' . $tableName . ' .form-group").hasClass("has-success")) 
            $("#form-' . $tableName . ' .form-group").removeClass("has-success");

        $("#modalDialog-' . $tableName. '").find("input#inputState").val("add");                
        $("#modalDialog-' . $tableName. '").modal();                
    });
    
    $("#modalDialog-' . $tableName. '").on("hide.bs.modal", function() {
        ' . $jscriptAddFunction . '
            
        if ($("#form-' . $tableName . ' .form-group").hasClass("has-error")) {
            $("#form-' . $tableName . ' .form-group").removeClass("has-error");
            $("#form-' . $tableName . ' .help-block").empty();
        }
        
        if ($("#form-' . $tableName . ' .form-group").hasClass("has-success")) 
            $("#form-' . $tableName . ' .form-group").removeClass("has-success");
    }); 
    
    $(document).on("click", "a#aEdit-' . $tableName .'", function(event){
        event.preventDefault();

        var thisObj = $(this).parent().parent();'  .
        
        $jscriptEditFunction . '    
        
        $("#modalDialog-' . $tableName. '").find("input#inputState").val("edit");
        $("#modalDialog-' . $tableName. '").find("input#currentIndexTrx").val(thisObj.find("input.indexTrx").val());
        $("#modalDialog-' . $tableName. '").modal();
    });

    $(document).on("click", "a#aDelete-' . $tableName .'", function(event){
        event.preventDefault();

        var remove = true;

        $(this).parent().parent().find("input").each(function(i, val) {                    

            $(val).attr("name", $(val).attr("name").replace("Edited", "Deleted"));

            if ($(val).attr("name").indexOf("Deleted") > -1) {
                remove = false;
            }
        });

        $(this).parent().parent().parent().fadeOut(500, function() {
            if (remove)
                $(this).remove();
        });
    });   
    
    var indexInput = ' . $this->params['indexRow'] . ';
    
    $("#form-' . $tableName . '").on("beforeSubmit", function(event) {
        if (!$("#form-' . $tableName . ' .form-group").hasClass("has-error")) {
            var state = $(this).find("input#inputState").val();
            if (state == "add") {
                var comp = $("#table-' . $tableName . '").clone();
                    
                var inputIndexTrx = $("<input>").attr("type", "hidden").attr("name", "indexTrx" + indexInput).attr("id", "indexTrx" + indexInput).attr("class", "indexTrx").attr("value", indexInput);                
                comp.children().find("div#groupButtonAction").parent().append(inputIndexTrx);';

foreach ($formFields as $field => $formField) {
    if ($formField['type'] !== null) {
        $jscript .= '
                    var input' . $field .' = $("<input>").attr("type", "hidden").attr("name", $(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").attr("name").replace("[]", "[" + indexInput + "]")).attr("class", "' . $tableName . '-' . $field . '").attr("value", $(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $field . '").val());                        
                    comp.children().find("div#groupButtonAction").parent().append(input' . $field .');
        ';
    }
}

foreach ($tableFields as $field => $tableField) {
    if ($formFields[$tableField['formField']]['type'] == 'textinput' || $formFields[$tableField['formField']]['type'] == 'textarea') {
        $jscript .= '                
                comp.children().find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
        ';
    } else if ($formFields[$tableField['formField']]['type'] == 'dropdown') {
        if (empty($formFields[$tableField['formField']]['noAdding'])) {
            $jscript .= '                
                    comp.children().find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").select2("data")[0].text);
            ';
        } else {
            $jscript .= '                
                    comp.children().find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
            ';
        }
    } else if ($formFields[$tableField['formField']]['type'] == 'money') {
        $jscript .= '                
                comp.children().find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
                comp.children().find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").currency({' . Yii::$app->params['currencyOptions'] . '});
        ';
    }
}

$jscript .= '
                $("#dataTable-' . $tableName . ' tbody").append(comp.children().html());
                $("#dataTable-' . $tableName . ' tbody").find("a#aEdit-' . $tableName .'").tooltip();
                $("#dataTable-' . $tableName . ' tbody").find("a#aDelete-' . $tableName .'").tooltip();

                $("#modalDialog-' . $tableName. '").modal("hide");
                    
                indexInput++;
            } else if (state == "edit") {
                var indexTrx = $(this).find("input#currentIndexTrx").val();                                
                var rowObj = $("#dataTable-' . $tableName . ' tbody").find("input#indexTrx" + indexTrx).parent().parent();';        

foreach ($tableFields as $field => $tableField) {
    if ($formFields[$tableField['formField']]['type'] == 'textinput' || $formFields[$tableField['formField']]['type'] == 'textarea') {
        $jscript .= '                                
                rowObj.find("input.' . $tableName . '-' . $tableField['formField'] . '").val($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
                    
                rowObj.find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
        ';
    } else if ($formFields[$tableField['formField']]['type'] == 'dropdown') {
        if (empty($formFields[$tableField['formField']]['noAdding'])) {
            $jscript .= '                                
                    rowObj.find("input.' . $tableName . '-' . $tableField['formField'] . '").val($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());

                    rowObj.find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").select2("data")[0].text);
            ';
        } else {
            $jscript .= '                                
                    rowObj.find("input.' . $tableName . '-' . $tableField['formField'] . '").val($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());

                    rowObj.find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
            ';
        }
    } else if ($formFields[$tableField['formField']]['type'] == 'money') {
        $jscript .= '                                
                rowObj.find("input.' . $tableName . '-' . $tableField['formField'] . '").val($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
                    
                rowObj.find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").html($(this).find("#' . strtolower(Inflector::camelize($tableName)) . '-' . $tableField['formField'] . '").val());
                rowObj.find("td span#data-'. $tableName. '-' . str_replace('.', '_', $field) . '").currency({' . Yii::$app->params['currencyOptions'] . '});
        ';
    }
}

$jscript .= '
                $("#modalDialog-' . $tableName. '").modal("hide");
            }
        }
        
        return false;
    });
';

$this->registerJs($jscript . $jscriptAdding); ?>