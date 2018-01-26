<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\MtableCategory;
use restotech\standard\backend\models\Mtable;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * MtableCategoryController implements the CRUD actions for MtableCategory model.
 */
class MtableCategoryController extends BackendController
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

            $modelMtableCategory = new MtableCategory();
            $modelMtableCategory->id = '9999';
            $modelMtableCategory->nama_category = 'Ruang Utama';
            $modelMtableCategory->keterangan = 'Generate dari inisialisasi';
            
            $modelMtable = new Mtable();
            $modelMtable->id = '9999';
            $modelMtable->mtable_category_id = '9999';
            $modelMtable->nama_meja = 'Meja Utama';
            $modelMtable->keterangan = 'Generate dari inisialisasi';

            $transaction = Yii::$app->db->beginTransaction();
            
            $flag = $modelMtableCategory->save() && $modelMtable->save();                        

            if ($flag) {
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

        $model = Mtable::find()
                ->joinWith(['mtableCategory'])
                ->andWhere(['mtable.id' => '9999'])
                ->andWhere(['mtable_category.id' => '9999'])
                ->asArray()->all();

        return $this->render('init', [
            'initialized' => empty($model) ? false : true,
        ]);
    }
    
    /**
     * Finds the MtableCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MtableCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MtableCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
