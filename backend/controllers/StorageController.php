<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\Storage;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StorageController implements the CRUD actions for Storage model.
 */
class StorageController extends BackendController
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

    public function actionInit() {

        if (Yii::$app->request->isPost) {

            $modelStorage = new Storage();
            $modelStorage->id = '9999';
            $modelStorage->nama_storage = 'Gudang Utama';
            $modelStorage->keterangan = 'Generate dari inisialisasi';

            $transaction = Yii::$app->db->beginTransaction();

            if ($modelStorage->save()) {
                Yii::$app->session->setFlash('status', 'success');
                Yii::$app->session->setFlash('message1', 'Inisialisasi Data Sukses');
                Yii::$app->session->setFlash('message2', 'Proses inisialisasi data sukses. Data telah berhasil disimpan.');

                $transaction->commit();
            } else {
                Yii::$app->session->setFlash('status', 'danger');
                Yii::$app->session->setFlash('message1', 'Inisialisasi Data Gagal');
                Yii::$app->session->setFlash('message2', 'Proses inisialisasi data gagal. Data gagal disimpan.');

                $transaction->rollBack();
            }

            return $this->redirect(['init']);
        }

        $model = Storage::find()
                ->andWhere(['id' => '9999'])
                ->asArray()->all();

        return $this->render('init', [
            'initialized' => empty($model) ? false : true,
        ]);
    }

    /**
     * Finds the Storage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Storage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Storage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
