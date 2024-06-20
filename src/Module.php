<?php

namespace yagas\debug;

use yii\debug\Module as OriginModule;

class Module extends OriginModule
{
    public $logTarget = ['class' => 'yagas\debug\LogTarget', 'app_no' => 'service_001'];
}
