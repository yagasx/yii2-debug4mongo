<?php

namespace yagas\debug\controllers;

use yii\debug\controllers\DefaultController as OriginDefaultController;
use yagas\debug\models\search\Debug;
use yii\web\NotFoundHttpException;

class DefaultController extends OriginDefaultController
{
    private $_manifest;

    public function actionIndex()
    {
        $searchModel = new Debug();
        $dataProvider = $searchModel->search($_GET);

        // load latest request
        $manifest = $dataProvider->getModels();
        $tags = array_keys(array_column($manifest, null, 'tag'));
        $tag = reset($tags);
        $this->loadData($tag);

        return $this->render('index', [
            'panels' => $this->module->panels,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'manifest' => $manifest,
        ]);
    }

    /**
     * @param bool $forceReload
     * @return array
     */
    protected function getManifest($forceReload = false)
    {
        if ($this->_manifest === null || $forceReload) {
            if ($forceReload) {
                clearstatcache();
            }
            $this->_manifest = $this->module->logTarget->loadManifest();
        }

        return array_column($this->_manifest, null, 'tag');
    }
}
