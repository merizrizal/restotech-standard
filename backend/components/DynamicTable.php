<?php

namespace backend\components;

use yii\base\Widget;

class DynamicTable extends Widget {
    
    public $model = null;
    public $tableFields = [];
    public $dataProvider = null;
    public $title = '';
    public $columnClass = '';
    
    public function tableData() {
        return $this->render('dynamicTableData', [
            'model' => $this->model,
            'tableFields' => $this->tableFields,
            'title' => $this->title,
            'columnClass' => $this->columnClass,
            'dataProvider' => $this->dataProvider,
        ]);
    }
    
}
