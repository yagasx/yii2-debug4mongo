<?php

namespace yagas\debug\models\search;

use yii\data\ArrayDataProvider;
use yii\debug\components\search\Filter;
use yii\debug\models\search\Debug as OriginDebug;

class Debug extends OriginDebug
{
    public $app_no;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['app_no', 'tag', 'ip', 'method', 'ajax', 'url', 'statusCode', 'sqlCount', 'mailCount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'app_no' => 'Application',
            'tag' => 'Tag',
            'processingTime' => 'Processing Time',
            'peakMemory' => 'Peak Memory',
            'ip' => 'Ip',
            'method' => 'Method',
            'ajax' => 'Ajax',
            'url' => 'url',
            'statusCode' => 'Status code',
            'sqlCount' => 'Query Count',
            'mailCount' => 'Mail Count',
        ];
    }

    public function search($params, $models)
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'sort' => [
                'attributes' => ['app_no', 'method', 'ip', 'tag', 'time', 'statusCode', 'sqlCount', 'mailCount', 'processingTime', 'peakMemory'],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $filter = new Filter();
        $this->addCondition($filter, 'app_no', true);
        $this->addCondition($filter, 'tag', true);
        $this->addCondition($filter, 'ip', true);
        $this->addCondition($filter, 'method');
        $this->addCondition($filter, 'ajax');
        $this->addCondition($filter, 'url', true);
        $this->addCondition($filter, 'statusCode');
        $this->addCondition($filter, 'sqlCount');
        $this->addCondition($filter, 'mailCount');
        $dataProvider->allModels = $filter->filter($models);

        return $dataProvider;
    }
}
