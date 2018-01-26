<?php

namespace restotech\standard\backend\controllers;

use Yii;
use restotech\standard\backend\models\PaymentMethod;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * PaymentMethodController implements the CRUD actions for PaymentMethod model.
 */
class PaymentMethodController extends BackendController
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
            
            $model = [];
            
            $model[0] = new PaymentMethod();
            $model[0]->id = '9999';
            $model[0]->nama_payment = 'Cash';
            $model[0]->type = 'Sale';
            $model[0]->method = 'Cash';
            $model[0]->keterangan = 'Generate dari inisialisasi';
            
            $model[1] = new PaymentMethod();
            $model[1]->id = '9998';
            $model[1]->nama_payment = 'Hutang';
            $model[1]->type = 'Sale';
            $model[1]->method = 'Account-Receiveable';
            $model[1]->keterangan = 'Generate dari inisialisasi';
            
            $model[2] = new PaymentMethod();
            $model[2]->id = '9997';
            $model[2]->nama_payment = 'Kartu Debit';
            $model[2]->type = 'Sale';
            $model[2]->method = 'Debit-Card';
            $model[2]->keterangan = 'Generate dari inisialisasi';
            
            $transaction = Yii::$app->db->beginTransaction();
            $flag = false;
            
            foreach ($model as $value) {
                
                if (!($flag = $value->save())) {
                    break;
                }
            }
            
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
        
        $model = PaymentMethod::find()
                ->andWhere(['id' => ['9999', '9998', '9997']])
                ->asArray()->all();
        
        if (empty($model)) {
            
            return $this->render('init', [
                
            ]);
        } else {
            
            $model = new PaymentMethod();

            $dataProvider = new ActiveDataProvider([
                'query' => PaymentMethod::find()->andWhere(['id' => ['9999', '9998', '9997']]),
                'pagination' => false,
            ]);
            
            return $this->render('data', [
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Finds the PaymentMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PaymentMethod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentMethod::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
