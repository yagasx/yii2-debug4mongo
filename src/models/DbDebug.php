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
 * @property array $summary 摘要信息
 * @property array $data 请求数据
 */
class DbDebug extends ActiveRecord
{
    public function attributes()
    {
        return ['_id', 'app_no', 'summary', 'data', 'datetime'];
    }

    public function rules()
    {
        return [
            ['datetime', 'default', 'value' => time()],
            [['app_no', 'summary', 'data'], 'safe']
        ];
    }
}