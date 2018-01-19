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
class AdminlteAssets extends AssetBundle
{
    public $basePath = '@rootUrl/admin/media';
    public $baseUrl = '@rootUrl/admin/media';
    
    public $css = [
        'css/AdminLTE.css',
        'css/skins/_all-skins.min.css',
    ];
    public $js = [
        'js/AdminLTE/app.js',
        'js/AdminLTE/custom.js',
    ];
    public $depends = [
        'backend\assets\AppAsset',
    ];
}
