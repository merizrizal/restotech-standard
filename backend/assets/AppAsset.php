<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/media';
    public $baseUrl = '@web/media';
    
    public $css = [
        'css/site.css',
    ];
    public $js = [
        
    ];
    public $depends = [
        'common\assets\AppAsset',
    ];
}
