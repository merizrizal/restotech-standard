<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace restotech\standard\backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@restotech/standard/backend/media';
    
    public $css = [
        'css/site.css',
    ];
    public $js = [
        
    ];
    public $depends = [
        'restotech\standard\common\assets\AppAsset',
    ];
}
