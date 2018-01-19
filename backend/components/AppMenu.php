<?php

namespace backend\components;

use yii\base\Widget;

class AppMenu extends Widget {

    public function header() {
        return $this->render('appHeader', array(
            
        ));
    }
    
    public function sideMenu() {
        return $this->render('appMenu', array(
            
        ));
    }
}
