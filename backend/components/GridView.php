<?php

namespace backend\components;

use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

class GridView extends \kartik\grid\GridView {
    
    public $scriptAfterPjax;    
    
    /**
     * Ends the PJAX container
     */
    protected function endPjax()
    {
        if (!$this->pjax) {
            return;
        }
        
        echo ArrayHelper::getValue($this->pjaxSettings, 'afterGrid', '');
                
        echo $this->scriptAfterPjax;
            
        Pjax::end();
    }
}
