<?php

namespace restotech\standard\api\controllers\frontend;

use Yii;
use restotech\standard\backend\models\MenuCategory;
use restotech\standard\backend\models\Menu;
use restotech\standard\backend\models\MenuCondiment;
use yii\filters\VerbFilter;

/**
 * Data controller
 */
class DataController extends \yii\rest\Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return array_merge(
            [],
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'search-menu' => ['post'],
                        'menu-category' => ['post'],
                        'menu' => ['post'],
                    ],
                ],
            ]);
    }

    public function actionDatetime() {

        Yii::$app->formatter->timeZone = 'Asia/Jakarta';

        $datetime = [];

        $datetime['datetime'] = Yii::$app->formatter->asDatetime(time(), 'dd-MM-yyyy HH:mm');

        return $datetime;
    }

    public function actionSearchMenu() {

        $post = Yii::$app->request->post();

        $modelMenu = Menu::find()
                ->joinWith([
                    'menuRecipes',
                    'menuRecipes.itemSku',
                    'menuRecipes.itemSku.stocks',
                    'menuCategory',
                    'menuCategory.menuCategoryPrinters',
                    'menuCategory.menuCategoryPrinters.printer0',
                    'menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                ])
                ->andFilterWhere(['LIKE', 'menu.nama_menu', $post['namaMenu']])
                ->andWhere(['menu.not_active' => 0])
                ->andWhere(['menu.is_deleted' => 0])
                ->asArray()->all();

        return [
            'modelMenu' => $modelMenu,
            'search' => true,
        ];
    }

    public function actionMenuCategory() {

        $post = Yii::$app->request->post();

        $modelMenuCategory = MenuCategory::find();

        if (!empty($post['id'])) {
            $modelMenuCategory = $modelMenuCategory->andWhere(['menu_category.parent_category_id' => $post['id']]);
        } else {
            $modelMenuCategory = $modelMenuCategory->andWhere(['IS', 'menu_category.parent_category_id', NULL]);
        }

        $modelMenuCategory = $modelMenuCategory
                ->andWhere(['menu_category.not_active' => 0])
                ->asArray()->all();

        return [
            'modelMenuCategory' => $modelMenuCategory,
            'pid' => !empty($post['id']) ? $post['id'] : null,
        ];
    }

    public function actionMenu() {

        $post = Yii::$app->request->post();

        $modelMenu = Menu::find()
                ->joinWith([
                    'menuRecipes',
                    'menuRecipes.itemSku',
                    'menuRecipes.itemSku.stocks',
                    'menuCategory',
                    'menuCategory.menuCategoryPrinters',
                    'menuCategory.menuCategoryPrinters.printer0',
                    'menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                ])
                ->andWhere(['menu.menu_category_id' => $post['id']])
                ->andWhere(['menu.not_active' => 0])
                ->andWhere(['menu.is_deleted' => 0])
                ->asArray()->all();

        return [
            'modelMenu' => $modelMenu,
            'cid' => !empty($modelMenu[0]['menuCategory']['parent_category_id']) ? $modelMenu[0]['menuCategory']['parent_category_id'] : MenuCategory::find()->andWhere(['id' => $post['id']])->asArray()->one()['parent_category_id'],
            'search' => false,
        ];
    }

    public function actionCondiment() {

        $post = Yii::$app->request->post();

        $modelMenu = MenuCondiment::find()
                ->joinWith([
                    'menu',
                    'menu.menuRecipes',
                    'menu.menuRecipes.itemSku',
                    'menu.menuRecipes.itemSku.stocks',
                    'menu.menuCategory',
                    'menu.menuCategory.menuCategoryPrinters',
                    'menu.menuCategory.menuCategoryPrinters.printer0',
                    'menu.menuCategory.parentCategory' => function($query) {
                        $query->from('menu_category parent_menu_category');
                    },
                ])
                ->andWhere(['menu_condiment.parent_menu_id' => $post['parent_id']])
                ->andWhere(['menu.not_active' => 0])
                ->andWhere(['menu.is_deleted' => 0])
                ->asArray()->all();

        return [
            'modelMenu' => $modelMenu,
            'orderParentId' => $post['order_parent_id'],
        ];
    }
}