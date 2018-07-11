<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class jQueryAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery/dist';
    public $js = [
        'jquery.js',
        'jquery.min.js',
        'app.min.js',
    ];
    public $css = [];
}
