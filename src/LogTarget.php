<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yagas\debug;

use yagas\debug\models\search\Debug;
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
        $dbDebug->setAttributes($summary);
        $dbDebug->data = $data;

        if (!$dbDebug->save()) {
            $errors = $dbDebug->firstErrors;
            throw new ErrorException(current($errors));
        }
        $this->mongo_gc(); // 执行清理历史记录
    }

    /**
     * @see DefaultController
     * @return array
     */
    public function loadManifest()
    {
        $debug = new Debug();
        $dataProvider = $debug->search($_GET);
        return $dataProvider->getModels();
    }

    /**
     * @see DefaultController
     * @return array
     */
    public function loadTagToPanels($tag)
    {
        $record = DbDebug::find()->where(['tag' => $tag])->one();
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

    protected function mongo_gc()
    {
        $random = mt_rand(1, 100);
        if ($random <= $this->module->percent) {
            $days = $this->module->historySize;
            $datetime = strtotime("-{$days} day");
            DbDebug::deleteAll(['<', 'time', $datetime]);
        }
    }
}
