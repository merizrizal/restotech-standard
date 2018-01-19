<?php

namespace backend\components;

use yii\base\Widget;

class Modal extends Widget {
    
    public $id = 'modalDialog';
    public $status = '';
    public $title = '';
    public $body = '';   
    
    public function onHidden($script) {
        $jscript = '
            $("#' . $this->id . '").on("hidden.bs.modal", function(event) {
                ' . $script . '
            });
        ';
        
        $this->getView()->registerJs($jscript);
    }
    
    public function renderDialog() {
        return $this->render('modal', [
            'id' => $this->id,
            'status' => $this->status,
            'title' => $this->title,
            'body' => $this->body,
        ]);
    }
}
