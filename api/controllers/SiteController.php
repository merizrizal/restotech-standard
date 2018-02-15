<?php
namespace restotech\standard\api\controllers;

use Yii;

use yii\filters\VerbFilter;
use restotech\standard\backend\components\Tools;

/**
 * Site controller
 */
class SiteController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            [],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $this->layout = 'zero';

        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin() {
        
    }

    public function actionLogout() {
        
    }

    public function actionPrint() {
        $data = !empty(Yii::$app->request->post()) ? Yii::$app->request->post() : Yii::$app->request->queryParams;

        $return = [];

        $return['flag'] = Tools::printToServer(\yii\helpers\Json::encode($data));

        return $return;
    }


    public function actionGetDatetime() {

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';
        $datetime = [];
        $datetime['date'] = Yii::$app->formatter->asDatetime(time(), 'EEEE, d LLLL yyyy');
        $datetime['time'] = Yii::$app->formatter->asDatetime(time(), 'HH:mm');

        return $datetime;
    }
}