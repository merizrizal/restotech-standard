<?php

namespace backend\components;

use yii\base\Widget;

class DynamicForm extends Widget {
    
    public $model = null;
    public $tableFields = [];
    public $inputFields = [];
    public $formFields = [];
    public $dataProvider = null;
    public $title = '';
    public $columnClass = '';
    public $actionButton = [];
    public $type = 'backend';
    
    public function tableData() {
        return $this->render('dynamicFormTableData', [
            'model' => $this->model,
            'tableFields' => $this->tableFields,
            'inputFields' => $this->inputFields,
            'formFields' => $this->formFields,
            'title' => $this->title,
            'columnClass' => $this->columnClass,
            'dataProvider' => $this->dataProvider,
            'actionButton' => $this->actionButton,
        ]);
    }
    
    public function component() {
        return $this->render('dynamicFormComponent', [
            'model' => $this->model,
            'tableFields' => $this->tableFields,
            'inputFields' => $this->inputFields,
            'formFields' => $this->formFields,
            'title' => $this->title,
            'type' => $this->type,
        ]);
    }
}
