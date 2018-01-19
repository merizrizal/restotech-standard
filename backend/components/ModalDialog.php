<?php

namespace backend\components;

use yii\base\Widget;

class ModalDialog extends Widget {
    
    public $clickedComponent = '';
    public $modelAttributeId = '';
    public $modelAttributeName = '';            

    public function getScript() {
        
        $jscript = '$("' . $this->clickedComponent . '").on("click", function(e) {'
                        . 'e.preventDefault();'
                        . 'var aButton = $(this);'                        
                        . '$("#modalDeleteConfirmation").on("show.bs.modal", function (e) {'
                                . 'var modal = $(this);'
                                . 'modal.find(".box-title").text("Delete Data: (" + aButton.attr("' . $this->modelAttributeId . '") + ") " + aButton.attr("' . $this->modelAttributeName . '"));'
                                . 'modal.find("a#aYes").attr("href", aButton.attr("href"));'
                        . '});'
                        . '$("#modalDeleteConfirmation").modal();'
                    . '});';
        
        return $jscript;       
    }
    
    public function theScript() {
     
        $this->getView()->registerJs($this->getScript());
    }

    public function renderDialog() {
        return $this->render('modalDialog', [
            
        ]);
    }
}
