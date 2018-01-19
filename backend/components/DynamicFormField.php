<?php

namespace restotech\standard\backend\components;

use yii\base\Widget;

class DynamicFormField extends Widget {
    
    public $dataModel = null;
    public $form = null;
    public $formFields = [];
    public $title = '';    
    public $columnClass = '';    
    
    public function component() {
        
        return $this->render('dynamicFormFieldComponent', [
            'dataModel' => $this->dataModel,
            'form' => $this->form,
            'formFields' => $this->formFields,
            'title' => $this->title,
            'columnClass' => $this->columnClass,
        ]);
    }
}

