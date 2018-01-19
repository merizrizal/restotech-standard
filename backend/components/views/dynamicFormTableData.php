<?php
use yii\helpers\Html;
use yii\helpers\Inflector;
use restotech\standard\backend\components\GridView; 

$assetCommon = $this->assetBundles['restotech\standard\common\assets\AppAsset'];

$this->params['indexRow'] = 0;
$this->params['inputFields'] = $inputFields; 

$tableName = $model[0]->tableName(); 

$widthButtonContainer = 75; 
$button = '';
$buttons = [];

if (!empty($actionButton) && count($actionButton) > 0) {
    $widthButtonContainer += count($actionButton) * 25;
    
    foreach ($actionButton as $key => $value) {
        $button .= '{' . $key . '}';
    }
    
    
}

$buttons = [
    'row' => function($url, $model, $key) {
        $index = $this->params['indexRow']++;

        $tableName = $model->tableName();

        $content = '
            <input name="indexTrx' . $index . '" id="indexTrx' . $index . '" class="indexTrx" type="hidden" value="' . $index . '">                                                                    
        ';

        foreach ($this->params['inputFields'] as $inputField) {
            $content .= '<input id="' . $tableName . '-' . $inputField . '_edited" class="' . $tableName . '-' . $inputField . '" type="hidden" name="' . Inflector::camelize($tableName) . 'Edited[' . $index . '][' . $inputField . ']" value="' . $model->$inputField . '">';
        }

        return $content;
    },
    'update' => function($url, $model, $key) {
        return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url, [
            'id' => 'aEdit-' . $model->tableName(),
            'class' => 'btn btn-success',
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => 'Edit',
        ]);
    },
    'delete' => function($url, $model, $key) {
        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
            'id' => 'aDelete-' . $model->tableName(),
            'class' => 'btn btn-danger',                            
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'title' => 'Delete',
            'model-id' => $model->id,
            'model-name' => '',
        ]);
    },    
]; ?>

<div class="row">
    <div class="<?= $columnClass ?>">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">
                    <?= $title ?>
                </h3>
                <div class="box-tools">
                    <div class="input-group">
                        <a id="addButton-<?= $tableName ?>" class="btn btn-primary" style="color: white">
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Add
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="box-body table-responsive no-padding">
                <?php
                /*$serialColumn = [
                    'class' => 'yii\grid\SerialColumn',
                    'content' => function($model, $key, $index, $column) {
                        return '';
                    },
                ];*/                
                
                
                $actionColumn = [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{row}
                                <div id="groupButtonAction" class="btn-group btn-group-xs" role="group" style="width: ' . $widthButtonContainer . 'px">
                                        {update}{delete}' . $button . '
                                </div>',
                    'buttons' => array_merge($buttons, $actionButton),
                ];
                        
                $column = [];                
                
                foreach ($tableFields as $field => $tableField) {                    
                    $tableFieldTemp = [
                        'attribute' => $field,
                        'format' => 'raw',
                        'value' => function ($model, $index, $widget, $column) {                             
                            $tableFieldTemp = $column->attribute;
                            
                            $value = null;
                            
                            if (strpos($tableFieldTemp, '.') !== false) {
                                $temp = $model;
                                $explodingStr = explode('.', $tableFieldTemp);
                                foreach ($explodingStr as $explodingStrValue) {
                                    $temp = $temp->$explodingStrValue;
                                }
                                
                                $value = $temp;
                            } else {
                                $value = $model->$tableFieldTemp;
                            }
                            
                            return '<span id="data-' . $model->tableName() . '-' . str_replace('.', '_', $tableFieldTemp) . '">' . $value . '</span>';
                        },
                    ];
                    array_push($column, $tableFieldTemp);
                }
                
                array_push($column, $actionColumn); ?>
                
                <?= GridView::widget([
                    'options' => [
                        'id' => 'dataTable-' . $tableName,
                    ],
                    'dataProvider' => $dataProvider,
                    'bordered' => false,
                    'floatHeader' => true,
                    'panel' => [
                        'heading' => false,
                        'footer' => false,
                    ],
                    'toolbar' => '',
                    'columns' => $column,
                ]); ?>                                
                
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJsFile($assetCommon->baseUrl . '/plugins/jquery-currency/jquery.currency.js', ['depends' => 'yii\web\YiiAsset']);

$jscript = '';

foreach ($tableFields as $field => $tableField) {
    if (!empty($tableField['type'])) {
        if ($tableField['type'] == 'money') {
            $jscript .= '$("span#data-' . $tableName . '-' . str_replace('.', '_', $field) . '").currency({' . Yii::$app->params['currencyOptions'] . '});';
        }
    }
} 

$this->registerJs($jscript); ?>