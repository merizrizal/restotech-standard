<?php
namespace restotech\standard;

use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

	$modules = [];
        $modules['backend']['class'] = 'restotech\standard\backend\BackendModule';
        $modules['frontend']['class'] = 'restotech\standard\frontend\FrontendModule';
        $this->setModules($modules);
    }
}
