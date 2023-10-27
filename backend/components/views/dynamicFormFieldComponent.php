<?php
use yii\helpers\Html;
use yii\helpers\Inflector;
use kartik\money\MaskMoney;

kartik\select2\Select2Asset::register($this);
kartik\select2\ThemeKrajeeBs3Asset::register($this);

$assetCommon = $this->assetBundles['restotech\standard\common\assets\AppAsset'];

if (is_array($dataModel)) {
    $model = $dataModel[0];
} else {
    $model = $dataModel;
}

$widthButtonContainer = 75;
$button = '';
$buttons = [];

if (!empty($actionButton) && count($actionButton) > 0) {
    $widthButtonContainer += count($actionButton) * 30;

    foreach ($actionButton as $key => $value) {
        $button .= '{' . $key . '}';
    }
}

$index = 0;

?>

<div class="row">
    <div class="<?= $columnClass ?>">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">
                    <?= $title ?>
                </h3>
                <div class="box-tools">
                </div>
            </div>

            <div class="box-body table-responsive no-padding">
                <table class="table table-striped">
                    <thead>
                        <tr>

                            <?php
                            $columnCount = 1;

                            foreach ($formFields as $field => $option) {

                                echo '<th ' . (!empty($option['colOption']) ? $option['colOption'] : '') . '>' . $model->getAttributeLabel($field) . '</th>';

                                $columnCount++;
                            } ?>

                            <td style="width: <?= $widthButtonContainer ?>px"><i class="fa fa-trash"></i></td>

                        </tr>
                    </thead>
                    <tbody id="tbody-<?= $model->tableName() ?>">

                        <?php
                        $jscriptAdding = '';

                        if (is_array($dataModel)):

                            foreach ($dataModel as $i => $data):

                                echo '<tr id="data-' . $model->tableName() . '">';

                                foreach ($formFields as $field => $option):

                                    $disabled = !empty($option['existIsDisabled']) ? true : false; ?>

                                    <td>
                                        <?php
                                        if ($option['type'] == 'textinput' || $option['type'] == 'textinput-dropdown') {

                                            echo $form->field($data, '[' . $i . ']' . $field, [
                                                'template' => '{input}{error}'
                                            ])->textInput(['maxlength' => true, 'style' => 'width: 100%', 'disabled' => $disabled]);
                                        } else if ($option['type'] == 'dropdown') {

                                            echo $form->field($data, '[' . $i . ']' . $field, [
                                                'template' => '{input}{error}'
                                            ])->dropDownList($option['data'], ['style' => 'width: 100%', 'disabled' => $disabled]);

                                            $jscriptAdding .= '
                                                $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $field . '").select2({
                                                    theme: "' . kartik\select2\Select2::THEME_KRAJEE . '",
                                                    placeholder: "Pilih",
                                                    allowClear: true,
                                                });
                                            ';

                                            if (!empty($option['affect'])) {

                                                $affectField = $option['affect']['field'];

                                                $jscriptAdding .= '
                                                    var ' . Inflector::camelize($model->tableName() . '_' . $i . '_' . $affectField) . ' = function(remoteData) {
                                                        $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $affectField . '").val(null);
                                                        $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $affectField . '").select2({
                                                            theme: "' . kartik\select2\Select2::THEME_KRAJEE . '",
                                                            placeholder: "Pilih",
                                                            allowClear: true,
                                                            data: remoteData,
                                                        });
                                                    };

                                                    ' . Inflector::camelize($model->tableName() . '_' . $i . '_' . $affectField) . '([]);

                                                    $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $field . '").on("select2:select", function(e) {
                                                        $("input#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $affectField . '").val(null).trigger("change");

                                                        $.ajax({
                                                            dataType: "json",
                                                            cache: false,
                                                            url: "' . $option['affect']['url'] . '?id=" + $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $field . ' option:selected").val(),
                                                            success: function(response) {
                                                                ' . Inflector::camelize($model->tableName() . '_' . $i . '_' . $affectField) . '(response);
                                                            }
                                                        });
                                                    });

                                                    $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $field . '").on("select2:unselect", function(e) {
                                                        $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $affectField . '").val(null).trigger("change");
                                                        ' . Inflector::camelize($model->tableName() . '_' . $i . '_' . $affectField) . '([]);
                                                    });

                                                    $.ajax({
                                                        dataType: "json",
                                                        cache: false,
                                                        url: "' . $option['affect']['url'] . '?id=" + $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $field . ' option:selected").val(),
                                                        success: function(response) {
                                                            ' . Inflector::camelize($model->tableName() . '_' . $i . '_' . $affectField) . '(response);
                                                            $("#' . strtolower(Inflector::camelize($model->tableName())) . '-' . $i . '-' . $affectField . '").val("' . $data->$affectField . '").trigger("change");
                                                        }
                                                    });
                                                ';
                                            }
                                        } else if ($option['type'] == 'money') {

                                            echo $form->field($data, '[' . $i . ']' . $field, [
                                                'template' => '{input}{error}'
                                            ])->widget(MaskMoney::className(), ['disabled' => $disabled]);
                                        } ?>
                                    </td>

                                <?php
                                endforeach;

                                echo '<td>';
                                    echo $form->field($data, '[' . $i . ']id', ['template' => '{input}'])->hiddenInput();

                                    $data->id = null;

                                    echo $form->field($data, '[' . $i . '][delete]id', ['template' => '{input}'])->checkbox(null, false);
                                echo '</td>';

                                echo '</tr>';

                                $index = $i;

                            endforeach;
                        else: ?>

                            <tr id="no-data-<?= $model->tableName() ?>">
                                <td colspan="<?= $columnCount ?>">Tidak ada data</td>
                            </tr>

                        <?php
                        endif; ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="<?= $columnCount ?>">
                                <a id="addButton-<?= $model->tableName() ?>" class="btn btn-primary btn-sm pull-right" style="color: white">
                                    <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Add
                                </a>
                                <?= Html::hiddenInput('index', $index + 1, ['id' => 'index-' . $model->tableName()]) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<table id="table-temp-<?= $model->tableName() ?>" class="hide">
    <tbody>
        <tr id="data-<?= $model->tableName() ?>">
            <?php
            foreach ($formFields as $field => $option): ?>

                <td>
                    <?php

                    $model->$field = 1;

                    if ($option['type'] == 'textinput' || $option['type'] == 'textinput-dropdown') {

                        echo $form->field($model, '[index]' . $field, [
                            'template' => '{input}{error}',
                            'enableClientValidation' => false,
                        ])->textInput(['maxlength' => true, 'style' => 'width: 100%']);
                    } else if ($option['type'] == 'dropdown') {

                        echo $form->field($model, '[index]' . $field, [
                            'template' => '{input}{error}',
                            'enableClientValidation' => false,
                        ])->dropDownList($option['data'], ['style' => 'width: 100%']);
                    } else if ($option['type'] == 'money') {

                        echo $form->field($model, '[index]' . $field, [
                            'template' => '{input}{error}',
                            'enableClientValidation' => false,
                        ])->widget(MaskMoney::className());
                    }
                    ?>
                </td>

                <?php
            endforeach; ?>

            <td>
                <div class="btn-group btn-group-xs" role="group">
                    <?= Html::a('<i class="fa fa-trash"></i>', null, [
                        'id' => 'aDelete-' . $model->tableName(),
                        'class' => 'btn btn-danger',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'title' => 'Delete',
                    ]) ?>
                </div>
            </td>

        </tr>
    </tbody>
</table>

<?php
$this->registerCssFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/all.css', ['depends' => 'yii\web\YiiAsset']);

$this->registerJsFile($this->params['assetCommon']->baseUrl . '/plugins/iCheck/icheck.min.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '

    var maskMoney_setting = {"prefix":"Rp ","suffix":"","affixesStay":true,"thousands":".","decimal":",","precision":0,"allowZero":false,"allowNegative":false};

    $("#addButton-' . $model->tableName() . '").on("click", function(event) {
        var content = $("#table-temp-' . $model->tableName() . '").children().children().clone();

        var index = parseFloat($(this).parent().children("#index-' . $model->tableName() . '").val());';

foreach ($formFields as $field => $option) {

    $validators = $model->getActiveValidators($field);

    $jscript .= '
        var inputClass' . $field . ' = content.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'").parent().attr("class");
        inputClass' . $field . ' = inputClass' . $field . '.replace("index", index);

        content.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'").parent().attr("class", inputClass' . $field . ');

        var inputName' . $field . ' = content.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'").attr("name");
        inputName' . $field . ' = inputName' . $field . '.replace("index", index);

        content.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'").attr("name", inputName' . $field . ');

        var inputId' . $field . ' = content.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'").attr("id");
        inputId' . $field . ' = inputId' . $field . '.replace("index", index);

        content.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'").attr("id", inputId' . $field . ');

        content.find("#" + inputId' . $field . ').val(null);

        var tableTbody = $("#tbody-' . $model->tableName() . '");

        tableTbody.append(content);

        $("#' . $form->getId() . '").yiiActiveForm("add", {
            id: inputId' . $field . ',
            name: inputName' . $field . ',
            container: ".field-" + inputId' . $field . ',
            input: "#" + inputId' . $field . ',
            validate: function(attribute, value, messages, deferred, $form) {';

    foreach ($validators as $validator) {

        if (stripos($validator->className(), 'RequiredValidator') !== false) {

            $jscript .= '
                yii.validation.required(value, messages, {"message":"' . Inflector::camel2words(Inflector::camelize($field))  . ' tidak boleh kosong."});
            ';
        }

        if (stripos($validator->className(), 'NumberValidator') !== false) {

            $jscript .= '
                yii.validation.number(value, messages, {"pattern":/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/, "message":"' . Inflector::camel2words(Inflector::camelize($field))  . '  harus berupa angka.","skipOnEmpty":1});
            ';
        }
    }

    $jscript .= '
            },
        });
    ';

    if ($option['type'] == 'dropdown' || $option['type'] == 'textinput-dropdown') {

        $jscript .= '
        tableTbody.find("#" + inputId' . $field . ').select2({
            theme: "' . kartik\select2\Select2::THEME_KRAJEE . '",
            placeholder: "Pilih",
            allowClear: true
        });
        ';

        if (!empty($option['affect'])) {

            $affectField = $option['affect']['field'];

            $jscript .= '
            var inputIdAffect' . $affectField . ' = tableTbody.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $affectField .'").attr("id");
            inputIdAffect' . $affectField . ' = inputIdAffect' . $affectField . '.replace("index", index);

            var dropdownFunction' . $affectField . ' = function(remoteData) {

                $("#" + inputIdAffect' . $affectField . ').val(null);
                $("#" + inputIdAffect' . $affectField . ').select2({
                    theme: "' . kartik\select2\Select2::THEME_KRAJEE . '",
                    placeholder: "Pilih",
                    allowClear: true,
                    data: remoteData,
                });
            };

            $.ajax({
                dataType: "json",
                cache: false,
                url: "' . $option['affect']['url'] . '?id=" + tableTbody.find("#" + inputId' . $field . ' + " option:selected").val(),
                success: function(response) {
                    dropdownFunction' . $affectField . '(response);
                }
            });

            tableTbody.find("#" + inputId' . $field . ').on("select2:select", function(e) {

                var thisObj = $(this);

                $("input#" + inputIdAffect' . $affectField . ').val(null).trigger("change");

                $.ajax({
                    dataType: "json",
                    cache: false,
                    url: "' . $option['affect']['url'] . '?id=" + thisObj.find("option:selected").val(),
                    success: function(response) {
                        dropdownFunction' . $affectField . '(response);
                    }
                });
            });

            tableTbody.find("#" + inputId' . $field . ').on("select2:unselect", function(e) {
                $("#" + inputIdAffect' . $affectField . ').val(null).trigger("change");
                dropdownFunction' . $affectField . '([]);
            });
            ';
        }
    } else if ($option['type'] == 'money') {

        $jscript .= '
            var inputIdDisp' . $field . ' = tableTbody.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'-disp").attr("id");
            inputIdDisp' . $field . ' = inputIdDisp' . $field . '.replace("index", index);

            tableTbody.find("#' . strtolower(Inflector::camelize($model->tableName())) . '-index-' . $field .'-disp").attr("id", inputIdDisp' . $field . ');

            tableTbody.find("#" + inputId' . $field . ' + "-disp").maskMoney(maskMoney_setting);

            var val = parseFloat(tableTbody.find("#" + inputId' . $field . ').val());
            tableTbody.find("#" + inputId' . $field . ' + "-disp").maskMoney("mask", val);
            tableTbody.find("#" + inputId' . $field . ' + "-disp").on("change keyup", function (e) {
                if (e.type ==="change" || (e.type === "keyup" && (e.keyCode == 13 || e.which == 13))) {
                    var out = tableTbody.find("#" + inputId' . $field . ' + "-disp").maskMoney("unmasked")[0];
                    tableTbody.find("#" + inputId' . $field . ').val(out).trigger("change");
                }
            });
        ';
    }
}

$jscript .= '
        index++;
        $(this).parent().children("#index-' . $model->tableName() . '").val(index);

        if ($("tr#no-data-' . $model->tableName() .'").length > 0) {
            $("tr#no-data-' . $model->tableName() .'").hide();
        }
    });

    $(document).on("click", "a#aDelete-' . $model->tableName() .'", function(event){

        $(this).parent().parent().parent().fadeOut(180, function() {
            $(this).remove();

            if ($("tr#no-data-' . $model->tableName() .'").length > 0) {
                if ($("tr#data-' . $model->tableName() .'").length <= 1) {
                    $("tr#no-data-' . $model->tableName() .'").show();
                }
            }
        });

        return false;
    });
';

$this->registerJs($jscript . $jscriptAdding . Yii::$app->params['checkbox-radio-script']()); ?>