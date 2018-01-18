<?php

use frontend\components\NotificationDialog;


$status = Yii::$app->session->getFlash('status');
$message1 = Yii::$app->session->getFlash('message1');
$message2 = Yii::$app->session->getFlash('message2');

if ($status !== null): 
    $notif = new NotificationDialog([
        'status' => $status,
        'message1' => $message1,
        'message2' => $message2,
    ]);

    $notif->theScript();
    echo $notif->renderDialog();
    
    if (Yii::$app->session->getFlash('fail-end-day')) {
        
        $notif->onHidden('
            $("#modalAlertTransactionDay").modal("show");
        ');
    }    

endif; 

$this->title = 'List Table Category'; ?>

<div class="col-lg-12" style="padding-right: 33px">	
    <div class="row mt">

        <?php
        foreach ($modelMtable as $mtable): ?>            

            <a href="<?= Yii::$app->urlManager->createUrl(['page/index', 'cid' => $mtable['id']]) ?>">
                <div class="col-lg-3 col-md-4 col-sm-4 mb">
                    <div class="darkblue-panel">
                        <div class="darkblue-header" style="background-color: <?= $mtable['color'] ?>">
                            <h5 style="font-size: 20px;"><?= $mtable['nama_category'] ?></h5>
                        </div>

                        <div class="row" style="padding: 0 15px; height: 150px; background: url('<?= Yii::getAlias('@uploadsUrl') . '/img/mtable-category/thumb120x120' . $mtable['image'] ?>') no-repeat center">                                  
                            
                        </div><!-- /row -->
                    </div>
                </div>
            </a>

        <?php
        endforeach; ?>
            
    </div>     
</div><!-- /col-lg-9 END SECTION MIDDLE -->

