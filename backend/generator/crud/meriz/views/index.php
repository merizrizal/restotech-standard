<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "backend\\components\\GridView" : "yii\\widgets\\ListView" ?>;
use backend\components\ModalDialog;
use backend\components\NotificationDialog;

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

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

$this->title = <?= $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
$this->params['titleH1'] = '&nbsp;&nbsp;&nbsp;' . Html::a('<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;' . 'Create New Data', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?php \n" ?>
    $modalDialog = new ModalDialog([
        'clickedComponent' => 'a#delete',
        'modelAttributeId' => 'model-id',
        'modelAttributeName' => 'model-name',
    ]);
    
    $jscript = '$(\'[data-toggle="tooltip"]\').tooltip();'
            . $modalDialog->getScript();
            
    $this->registerJs($jscript);
    
    $jscript = '<script>' . $jscript . '</script>'; ?>
    
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'scriptAfterPjax' => $jscript,
        'bordered' => false,
        'floatHeader' => true,
        'panelHeadingTemplate' => '<div class="kv-panel-pager pull-right" style="text-align:right">'
                                    . '{pager}{summary}'
                                . '</div>'                                
                                . '<div class="clearfix"></div>'
        ,
        'panelFooterTemplate' => '<div class="kv-panel-pager pull-right" style="text-align:right">'
                                    . '{summary}{pager}'
                                . '</div>'
                                . '{footer}'
                                . '<div class="clearfix"></div>'
        ,
        'panel' => [
            'heading' => '',
        ],
        'toolbar' => [
            [
                'content' => Html::a('<i class="fa fa-repeat"></i>', ['index'], [
                            'data-pjax'=>false, 
                            'class' => 'btn btn-success', 
                            'data-placement' => 'top',
                            'data-toggle' => 'tooltip',
                            'title' => 'Refresh'
                ])
            ],
        ],
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-xs" role="group" style="width: 75px">'
                                    . '{view}{update}{delete}'
                            . '</div>',
                'buttons' => [
                    'view' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-search"></i>', $url, [
                            'id' => 'view',
                            'class' => 'btn btn-primary',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'View',
                        ]);
                    },
                    'update' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-pencil"></i>', $url, [
                            'id' => 'update',
                            'class' => 'btn btn-success',
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Edit',
                        ]);
                    },
                    'delete' =>  function($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'id' => 'delete',
                            'class' => 'btn btn-danger',                            
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'title' => 'Delete',
                            'model-id' => $model->id,
                            'model-name' => $model->name,
                        ]);
                    },
                ]
            ],
        ],
        'pager' => [
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right"></i>',
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>    

</div>

<?= "<?=" ?> $modalDialog->renderDialog() ?>