<?php
use yii\helpers\Html;
use restotech\standard\backend\components\Tools;

$settings_company_profile = Yii::$app->session->get('company_settings_profile'); ?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left">
                <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/company/', $settings_company_profile['company_image_file'], 200, 200, true), ['class' => 'img-responsive']) ?>
            </div>
            <div class="pull-left">
                <br>
                <p style="color: #b8c7ce">
                    <?= $settings_company_profile['company_name'] ?><br><br>
                    <?= $settings_company_profile['company_address'] ?><br>
                    <?= $settings_company_profile['company_city'] . ' ' . $settings_company_profile['company_postal_code'] ?><br><br>
                    <?= $settings_company_profile['company_phone'] ?><br>
                </p>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">NAVIGASI</li>

            <?php
            if (!empty(Yii::$app->params['navigation'])):
                foreach (Yii::$app->params['navigation'] as $navLevel1): ?>

                    <li class="<?= !empty($navLevel1['navigation']) ? 'treeview' : '' ?>">
                        <a href="<?= !empty($navLevel1['url']) ? Yii::$app->urlManager->createUrl($navLevel1['url']) : '#' ?>">
                            <i class="<?= $navLevel1['iconClass'] ?>"></i>
                            <span><?= $navLevel1['label'] ?></span>
                            <?= !empty($navLevel1['navigation']) ? '<i class="fa fa-angle-left pull-right"></i>' : '' ?>
                        </a>

                        <?php
                        if (!empty($navLevel1['navigation'])): ?>

                            <ul class="treeview-menu">

                                <?php
                                foreach ($navLevel1['navigation'] as $navLevel2): ?>

                                    <li>
                                        <a href="<?= Yii::$app->urlManager->createUrl($navLevel2['url']); ?>">
                                            <i class="<?= $navLevel2['iconClass'] ?>"></i><?= $navLevel2['label'] ?>
                                        </a>
                                    </li>

                                <?php
                                endforeach; ?>
                                    
                            </ul>
                                    
                            <?php
                        endif; ?>

                    </li>

                <?php
                endforeach;
            endif; ?>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>