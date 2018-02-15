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
        $modules['api']['class'] = 'restotech\standard\api\ApiModule';
        $this->setModules($modules);
    }
}
