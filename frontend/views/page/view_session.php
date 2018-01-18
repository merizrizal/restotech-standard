<?php
use yii\helpers\Html; ?>


<div class="col-lg-12">

    <div class="row mt">
        <div class="col-md-4 col-sm-4 mb">
            <div class="weather-2 pn" style="height: auto">
                
                <div class="weather-2-header">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6 goleft">
                            <span class="badge" style="margin: 0 0 10px 5px; font-size: 20px"><?= $modelTable->id ?></span>                                               
                        </div>
                    </div>
                </div><!-- /weather-2 header -->
                <div class="row centered">
                    <img src="<?= Yii::getAlias('@backend-web') ?>/img/mtable/thumb120x120<?= $modelTable->image ?>" class="img-circle" width="120">			
                </div>
                
                <div class="row data">
                    <div class="col-sm-6 col-xs-6 goleft">
                        <h4><b><?= $modelTable->nama_meja ?></b></h4>
                    </div>
                    <div class="col-sm-6 col-xs-6 goright">
                        <h6><?= $modelTable->kapasitas ?> chair</h6>
                    </div>
                </div>
                
                <div class="row data">
                    <div class="col-sm-12 col-xs-12 goleft mt mb">
                        <?= Html::a('<i class="fa fa-undo"></i> Back', Yii::$app->urlManager->createUrl(['page/index']), ['class' => 'btn btn-danger']) ?>
                    </div>                    
                </div>
            </div> 
            
        </div>
        
        <div class="col-md-8 col-sm-8 mb">
            <div class="white-panel pn" style="height: auto">
                <div class="white-header"></div>
                <div style="padding: 0 10px 15px 10px">
                    <div class="row goleft">
                        <div class="col-md-12 col-sm-12">
                            <?php
                            foreach ($modelMtableSession as $mtableSessionData): ?>
                            
                            <div class="alert alert-success" style="margin-bottom: 10px; padding: 10px 15px">
                                <div class="row">
                                    <div class="col-md-10 col-sm-10">
                                        <b><?= $mtableSessionData->id ?></b>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <?= Html::a('<i class="fa fa-sign-in" style="color: #FFF"></i>', Yii::$app->urlManager->createUrl(['page/view-table', 'id' => $mtableSessionData->id]), ['class' => 'btn btn-primary btn-sm pull-right']) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php
                            endforeach; ?>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>