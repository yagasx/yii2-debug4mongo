<?php

namespace yagas\debug\controllers;

use yii\debug\controllers\DefaultController as OriginDefaultController;
use yagas\debug\models\search\Debug;

class DefaultController extends OriginDefaultController
{

    public function actionIndex()
    {
        $searchModel = new Debug();
        $dataProvider = $searchModel->search($_GET, $this->getManifest());

        // load latest request
        $tags = array_keys($this->getManifest());

        if (empty($tags)) {
            throw new \Exception("No debug data have been collected yet, try browsing the website first.");
        }

        $tag = reset($tags);
        $this->loadData($tag);

        return $this->render('index', [
            'panels' => $this->module->panels,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'manifest' => $this->getManifest(),
        ]);
    }
}
