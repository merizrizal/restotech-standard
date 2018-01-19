<?php
use yii\helpers\Html;
use restotech\standard\backend\components\Tools;

$assetCommon = $this->assetBundles['restotech\standard\common\assets\AppAsset']; ?>


<header class="main-header">
    <a href="<?= Yii::$app->homeUrl ?>" class="logo">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b><?= Html::img(Yii::$app->urlManager->baseUrl . '/media/logo.png', ['class' => 'img-responsive']) ?></b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b><?= Html::encode(Yii::$app->name) ?></b></span>	
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">                       
                <!-- User Account: style can be found in dropdown.less -->
                <li>
                    <a href="javascript:;" id="bardatetime">
                        Date Time
                    </a>                    
                </li>
                
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span><?= Yii::$app->session->get('user_data')['employee']['nama'] ?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-red">
                            <img src="<?= Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/employee/', Yii::$app->session->get('user_data')['employee']['image'], 200, 200) ?>" class="img-circle" alt="User Image" />
                            <p>
                                <?= Yii::$app->user->identity->id . ' - (' . Yii::$app->user->identity->kd_karyawan. ') ' . Yii::$app->session->get('user_data')['employee']['nama'] ?>
                                <small><?= Yii::$app->session->get('user_data')['user_level']['nama_level'] ?></small>
                            </p>
                        </li>                                
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                
                            </div>
                            <div class="pull-right">
                                <a href="<?= Yii::$app->urlManager->createUrl('site/logout'); ?>" data-method="post" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<?php
$jscript = '    
    var datetimeStatus = function() {  
        var date = 0;
        var time = 0;
        $.when(
            $.ajax({
                type: "GET",
                url: "' . Yii::$app->urlManager->createUrl(['site/get-datetime']) . '",            
                success: function(data) {
                    date = data.date;
                    time = data.time
                }
            })
        ).done(function() {
            $("a#bardatetime").html("").append(date).append("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;").append(time);
        });
    };
    
    datetimeStatus();
    
    setInterval(function () {
        datetimeStatus();
    }, 1000 * 60);
';

$this->registerJs($jscript); ?>