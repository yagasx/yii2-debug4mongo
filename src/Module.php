<?php

namespace yagas\debug;

use yii\debug\Module as OriginModule;

class Module extends OriginModule
{
    public $controllerNamespace = 'yagas\debug\controllers';
    
    public $logTarget = ['class' => 'yagas\debug\LogTarget', 'app_no' => 'service_001'];

    public $percent = 10; // gc执行清除历史数据的几率（百分之）

    public function init()
    {
        \Yii::setAlias('@yagas/debug/controllers', __DIR__ . '/controllers');
        parent::init();
    }
}
