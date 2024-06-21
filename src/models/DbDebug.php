<?php

namespace yagas\debug\models;

use yii\mongodb\ActiveRecord;

/**
 * 请求数据容器
 * 
 * @author yagas<yagas@sina.com>
 * @date 2024-06-20
 * 
 * @property string $app_no 节点名称
 * @property array $data 请求数据
 */
class DbDebug extends ActiveRecord
{
    public function attributes()
    {
        return ['_id', 'app_no', 'method', 'ip', 'url', 'tag', 'ajax', 'time', 'statusCode', 'sqlCount', 'mailCount', 'mailFiles', 'processingTime', 'peakMemory', 'data'];
    }

    public function rules()
    {
        return [
            [['app_no', 'method', 'ip', 'url', 'tag', 'ajax', 'time', 'statusCode', 'sqlCount', 'mailCount', 'mailFiles', 'processingTime', 'peakMemory', 'data'], 'safe']
        ];
    }
}