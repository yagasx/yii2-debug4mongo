<?php

namespace yagas\debug\models\search;

use yii\data\ActiveDataProvider;
use yii\debug\models\search\Debug as OriginDebug;

class Debug extends OriginDebug
{
    /**
     * Returns data provider with filled models. Filter applied if needed.
     * @param array $params an array of parameter values indexed by parameter names
     * @param array $models data to return provider for
     * @return \yii\data\ArrayDataProvider
     */
    public function search($params, $models)
    {
        $dataProvider = new ActiveDataProvider([
            'allModels' => $models,
            'sort' => [
                'attributes' => ['method', 'ip', 'tag', 'time', 'statusCode', 'sqlCount', 'mailCount', 'processingTime', 'peakMemory'],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // $filter = new Filter();
        // $this->addCondition($filter, 'tag', true);
        // $this->addCondition($filter, 'ip', true);
        // $this->addCondition($filter, 'method');
        // $this->addCondition($filter, 'ajax');
        // $this->addCondition($filter, 'url', true);
        // $this->addCondition($filter, 'statusCode');
        // $this->addCondition($filter, 'sqlCount');
        // $this->addCondition($filter, 'mailCount');
        // $dataProvider->allModels = $filter->filter($models);

        return $dataProvider;
    }
}