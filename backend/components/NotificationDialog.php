<?php

namespace restotech\standard\backend\components;

use yii\base\Widget;

class NotificationDialog extends Widget {
    
    public $status = '';
    public $message1 = '';
    public $message2 = '';
    public $id = 'modalNotification';
    
    public function theScript() {
        $jscript = '$("#' . $this->id . '").modal();';
        
        $this->getView()->registerJs($jscript);       
    }
    
    public function onHidden($script) {
        $jscript = '
            $("#' . $this->id . '").on("hidden.bs.modal", function(event) {
                ' . $script . '
            });
        ';
        
        $this->getView()->registerJs($jscript);
    }
    
    public function renderDialog() {
        return $this->render('notificationDialog', [
            'status' => $this->status,
            'message1' => $this->message1,
            'message2' => $this->message2,
            'id' => $this->id,
        ]);
    }
}
