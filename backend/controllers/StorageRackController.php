<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\StorageRack;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * StorageRackController implements the CRUD actions for StorageRack model.
 */
class StorageRackController extends BackendController
{
    public function behaviors()
    {
        return array_merge(
            $this->getAccess(),
            [                
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        
                    ],
                ],
            ]);
    }
    
    public function actionGetStorageRack($id)
    {
        $data = StorageRack::find()->where(['storage_id' => $id])->orderBy('nama_rak')->asArray()->all();

        $row = [];

        foreach ($data as $key => $value) {
            $row[$key]['id'] = $value['id'];
            $row[$key]['text'] = $value['nama_rak'];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $row;
    }
    
    /**
     * Finds the StorageRack model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return StorageRack the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StorageRack::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
