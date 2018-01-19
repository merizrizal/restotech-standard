<?php

namespace restotech\standard\frontend\components;

use yii\base\Widget;

class NotificationDialog extends Widget {
    
    public $status = '';
    public $message1 = '';
    public $message2 = '';
    
    public function theScript() {
        $jscript = '$("#modalNotification").modal();';
        
        $this->getView()->registerJs($jscript);       
    }
    
    public function onHidden($script) {
        $jscript = '
            $("#modalNotification").on("hidden.bs.modal", function(event) {
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
        ]);
    }
}
