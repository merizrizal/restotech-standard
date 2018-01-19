<?php
use restotech\standard\backend\components\GridView; 

$tableName = $model->tableName(); ?>

<div class="row">
    <div class="<?= $columnClass ?>">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">
                    <?= $title ?>
                </h3>                
            </div>
            
            <div class="box-body table-responsive no-padding">
                <?php                                
                        
                $column = [];                                
                
                foreach ($tableFields as $tableField) {                    
                    array_push($column, $tableField);
                }  ?>
                
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
                        'before' => false,
                        'after' => false,
                    ],
                    'columns' => $column,
                ]); ?>                                
                
            </div>
        </div>
    </div>
</div>