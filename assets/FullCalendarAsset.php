<?php

namespace app\assets;

use yii\web\AssetBundle;

class FullCalendarAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/fullcalendar';
    public $js = [
        'fullcalendar.min.js',
    ];
    public $css = [
        'fullcalendar.min.css',
    ];
}
