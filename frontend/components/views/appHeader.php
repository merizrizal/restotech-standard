<?php

use yii\helpers\Html; ?>


<!-- **********************************************************************************************************************************************************
TOP BAR CONTENT & NOTIFICATIONS
*********************************************************************************************************************************************************** -->
<!--header start-->
<header class="header black-bg">
    <!--logo start-->
    <a href="<?= Yii::$app->urlManager->createUrl('home/index'); ?>" class="logo">
        <b><?= Html::encode(Yii::$app->name) ?></b>        
    </a>
    <!--logo end-->       
    <div class="hidden-lg hidden-md hidden-sm clearfix"></div>
    
    <div id="top_menu" class="nav notify-row">
        <ul class="nav top-menu">
            <li>
                <a href="<?= Yii::$app->urlManager->createUrl('home/index'); ?>" id="home" data-toggle="tooltip" data-placement="bottom" title="Home"><i class="fa fa-home"></i></a>
            </li>
            <li>
                <a href="<?= Yii::getAlias('@rootUrl') . '/' . Yii::$app->params['subprogram']['administrator']; ?>" data-toggle="tooltip" data-placement="bottom" title="Back Office"><i class="fa fa-database"></i></a>
            </li>
            <li>
                <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['standard'] . 'site/logout']); ?>" data-method="post" data-toggle="tooltip" data-placement="bottom" title="Logout"><i class="fa fa-sign-out"></i></a>
            </li>
        </ul>
    </div>
    
    <div class="top-menu">                        
        <ul class="nav pull-right top-menu">
            <li>
                <a class="bar">
                    <b><?= Yii::$app->session->get('user_data')['employee']['nama'] ?></b> (<?= Yii::$app->session->get('user_data')['user_level']['nama_level'] ?>)
                </a>
            </li>
            <li>
                <a id="bartransactionday" class="bar">
                    Transaction Day <b><?= !empty($this->params['transactionDay']) ? $this->params['transactionDay'] : '(not set)' ?></b>
                </a>               
            </li>
            <li>
                <a id="bardatetime" class="bar">
                    Date Time
                </a>                    
            </li>
        </ul>
    </div>
</header>
<!--header end-->

<?php
$jscript = '    
    var datetimeStatus = function() {  
        var date = 0;
        var time = 0;
        $.when(
            $.ajax({
                type: "GET",
                url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['standard'] . 'site/get-datetime']) . '",            
                success: function(data) {
                    date = data.date;
                    time = data.time
                }
            })
        ).done(function() {
            $("#bardatetime").html("").append(date).append("&nbsp;&nbsp;&nbsp;").append(time);
        });
    };
    
    datetimeStatus();
    
    setInterval(function () {
        datetimeStatus();
    }, 1000 * 60);
    
    $(\'[data-toggle="tooltip"]\').tooltip();
    
    var loadMenu = function() {
        
        $.ajax({
            cache: false,
            type: "POST",
            url: "' . Yii::$app->urlManager->createUrl([Yii::$app->params['posModule']['standard'] . 'home/load-menu'])  . '",
            beforeSend: function(xhr) {
                $(".overlay").show();
                $(".loading-img").show();
            },
            success: function(response) {
                $("#home-content").html(response);
                
                $(".overlay").hide();
                $(".loading-img").hide();                
            },
            error: function (xhr, ajaxOptions, thrownError) {     
                $("#home-content").html(xhr.responseText);
                
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
    };
    
    loadMenu();
    
    $("#home").on("click", function() { 
    
        loadMenu();
        
        return false;
    });
';

$this->registerJs($jscript); ?>