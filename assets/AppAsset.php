<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use dmstr\web\AdminLteAsset;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
        // 'css/custom.css',
        // 'css/_all-skins.min.css',
		// 'css/fullcalendar.min',
    ];
    public $js = [
    ];
    public $depends = [
        // 'dmstr\web\AdminLteAsset',
//        'dmstr\web\AdminLteCustomAsset',
//        'yii2mod\alert\AlertAsset',
    ];


}
