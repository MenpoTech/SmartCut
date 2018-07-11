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
class Select2AdminLteAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist/plugins/select2';
    public $js = [
        'select2.full.min.js',
    ];
    public $css = [
        'select2.min.css'
    ];
    public $depends = [
        'app\assets\jQueryAsset',
        'app\assets\AdminLteBootrapAsset'
    ];
}
