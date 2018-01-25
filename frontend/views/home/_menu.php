<?php
if (!empty(Yii::$app->params['navigation'])):
    foreach (Yii::$app->params['navigation'] as $navigation): ?>

        <div class="col-lg-3 col-md-3 col-sm-3 mb">
            <a href="<?= Yii::$app->urlManager->createUrl($navigation['url']) ?>" id="menu">
                <div class="home-menu pn centered">
                    <br><br>
                    <i class="<?= $navigation['iconClass'] ?>"></i>
                    <h1><?= $navigation['label'] ?></h1>            
                </div>
            </a>
        </div>

    <?php
    endforeach;
endif; ?>

<?php

$jscript = '
    
    $("a#menu").on("click", function() {
    
        $.ajax({
            cache: false,
            type: "POST",
            url: $(this).attr("href"),
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
                swal("Error", xhr.responseText, "error");
                console.log(xhr);
                $(".overlay").hide();
                $(".loading-img").hide();
            }
        });
        
        return false;
    });
';

$this->registerJs($jscript); ?>