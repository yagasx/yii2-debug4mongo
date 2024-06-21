<?php

namespace yagas\debug\models\search;

use yagas\debug\models\DbDebug;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\debug\components\search\Filter;
use yii\debug\models\search\Debug as OriginDebug;
use yii\helpers\ArrayHelper;

class Debug extends OriginDebug
{
    public $app_no;
    public $page = 1;
    public $per_page = 50;

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

    public function init()
    {
        $this->page = ArrayHelper::getValue($_GET, 'page', 1);
        $this->per_page = ArrayHelper::getValue($_GET, 'per-page', 50);
    }

    public function search($params, $models=null)
    {
        $this->load($params);
        $dataProvider = new ActiveDataProvider([
            'query' => DbDebug::find()->filterWhere([
                'method' => $this->method,
                'ajax' => $this->ajax,
                'statusCode' => $this->statusCode,
                'sqlCount' => $this->sqlCount,
                'mailCount' => $this->mailCount,
            ])->andFilterWhere(['like', 'url', $this->url])
                ->andFilterWhere(['like', 'tag', $this->tag])
                ->select(['app_no','method', 'ip', 'tag', 'ajax', 'url', 'time', 'statusCode', 'sqlCount', 'mailCount', 'processingTime', 'peakMemory'])
                ->asArray(),
            'sort' => [
                'defaultOrder' => ['time' => SORT_DESC],
                'attributes' => ['method', 'ip', 'tag', 'time', 'statusCode', 'sqlCount', 'mailCount', 'processingTime', 'peakMemory'],
            ],
            'pagination' => [
                'page' => ($this->page-1),
                'pageSize' => $this->per_page,
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public static function decode($models)
    {
        $data = [];
        foreach ($models as $item) {
            $row = $item['summary'];
            $row['app_no'] = $item['app_no'];
            $data[] = $row;
        }
        return array_column($data, null, 'tag');
    }
}
