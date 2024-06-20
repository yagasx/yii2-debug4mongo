<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yagas\debug;

use Yii;
use yii\helpers\FileHelper;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yagas\debug\models\DbDebug;
use yii\debug\FlattenException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\debug\LogTarget as OriginLogTarget;

/**
 * The debug LogTarget is used to store logs for later use in the debugger tool
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LogTarget extends OriginLogTarget
{
    public $app_no;

    /**
     * Exports log messages to a specific destination.
     * Child classes must implement this method.
     * @throws \yii\base\Exception
     */
    public function export()
    {
        $path = $this->module->dataPath;
        FileHelper::createDirectory($path, $this->module->dirMode);

        $summary = $this->collectSummary();
        $data = [];
        $exceptions = [];
        foreach ($this->module->panels as $id => $panel) {
            try {
                $panelData = $panel->save();
                if ($id === 'profiling') {
                    $summary['peakMemory'] = $panelData['memory'];
                    $summary['processingTime'] = $panelData['time'];
                }
                $data[$id] = serialize($panelData);
            } catch (\Exception $exception) {
                $exceptions[$id] = new FlattenException($exception);
            }
        }
        $data['summary'] = $summary;
        $data['exceptions'] = $exceptions;

        $dbDebug = new DbDebug();
        $dbDebug->app_no = $this->app_no;
        $dbDebug->summary = $summary;
        $dbDebug->data = $data;

        if (!$dbDebug->save()) {
            $errors = $dbDebug->firstErrors;
            throw new ErrorException(current($errors));
        }
    }

    /**
     * @see DefaultController
     * @return array
     */
    public function loadManifest()
    {
        $page = ArrayHelper::getValue($_GET, 'page', '1');
        $pageSize = ArrayHelper::getValue($_GET, 'per-page', '50');
        $dataProvider = new ActiveDataProvider([
            'query' => DbDebug::find()->select(['_id', 'app_no', 'summary'])->asArray(),
            'pagination' => ['page' => ($page - 1), 'pageSize' => $pageSize],
            'sort' => ['defaultOrder' => ['datetime' => SORT_DESC]]
        ]);

        return $this->afterLoadManifest($dataProvider->getModels());
    }

    public function afterLoadManifest($models)
    {
        $data = [];
        foreach ($models as $item) {
            $row = $item['summary'];
            $row['app_no'] = $item['app_no'];
            $data[] = $row;
        }
        return array_column($data, null, 'tag');
    }

    /**
     * @see DefaultController
     * @return array
     */
    public function loadTagToPanels($tag)
    {
        $record = DbDebug::find()->where(['summary.tag' => $tag])->one();
        if (!$record) {
            throw new NotFoundHttpException("Unable to find debug data tagged with '$tag'.");
        }
        $data = $record['data'];
        $exceptions = $data['exceptions'];
        foreach ($this->module->panels as $id => $panel) {
            if (isset($data[$id])) {
                $panel->tag = $tag;
                $panel->load(unserialize($data[$id]));
            } else {
                unset($this->module->panels[$id]);
            }
            if (isset($exceptions[$id])) {
                $panel->setError($exceptions[$id]);
            }
        }

        return $data;
    }
}
