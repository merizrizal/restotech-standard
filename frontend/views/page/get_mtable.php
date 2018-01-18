<?php
use yii\helpers\Html; 


if ($type == 'close'): ?>

    <div class="row">

        <?php                

        $flag = false;
        foreach ($modelMtable as $mtableData): 
            if (count($mtableData['mtableSessions']) == 0): 
                $flag = true; ?>

                <div class="col-md-2 col-sm-2">
                    <?= Html::button($mtableData['id'], ['class' => 'btn btn-success btn-block', 'id' => 'btnMtable']) ?>

                    <?php 
                    
                    echo Html::beginForm(Yii::$app->urlManager->createUrl(['page/transfer-mtable']));
                    echo Html::hiddenInput('mtableId', $mtableData['id']);
                    echo Html::hiddenInput('sessionMtableId', '', ['id' => 'inputSessionMtableId']);
                    echo Html::endForm(); ?>

                </div>

            <?php
            endif;
        endforeach; 

        if (!$flag): ?>

            <div class="col-md-12 col-sm-12">
                <br><br>
                Tidak ada meja yang kosong
                <br><br>
            </div>

        <?php
        endif; ?>

    </div>

    <script>

    $("button#btnMtable").on("click", function(event){
        $(this).parent().find("form").find("input#inputSessionMtableId").val($("#sessionMtable").val());
        $(this).parent().find("form").append($("input#billPrinted"));
        $(this).parent().find("form").submit();
    });

    </script>
    
<?php
else if ($type == 'open' || $type == 'open-join'):?>
    <div id="temp" style="display: none">
        <?= $row ?>
    </div>
    
    <div class="row">

        <?php                

        $flag = false;
        foreach ($modelMtable as $mtableData): 
            if (count($mtableData['mtableSessions']) > 0 && $mtableId !== $mtableData['id']): 
                $flag = true; ?>

                <div class="col-md-2 col-sm-2">
                    <?= Html::button($mtableData['id'], ['class' => 'btn btn-success btn-block', 'id' => 'btnMtable']) ?>

                    <?php 
                    $action = '';
                    if ($type == 'open')
                        $action = 'page/transfer-menu';
                    else if ($type == 'open-join')
                        $action = 'page/join-mtable';
                    
                    echo Html::beginForm(Yii::$app->urlManager->createUrl([$action]));
                    echo Html::hiddenInput('mtableId', $mtableData['id']);
                    echo Html::hiddenInput('activeMtableSessionId', '', ['id' => 'inputSessionMtableId']);
                    echo Html::hiddenInput('mtableSessionId', $mtableData['mtableSessions'][0]['id'], ['id' => 'mtableSessionId']);
                    echo Html::endForm(); ?>

                </div>

            <?php
            endif;
        endforeach; 

        if (!$flag): ?>

            <div class="col-md-12 col-sm-12">
                <br><br>
                Tidak ada meja yang diisi
                <br><br>
            </div>

        <?php
        endif; ?>

    </div>

    <script>

    $("button#btnMtable").on("click", function(event){
        $(this).parent().find("form").find("input#inputSessionMtableId").val($("#sessionMtable").val());
        $(this).parent().find("form").append($("input#billPrinted"));
        $(this).parent().find("form").append($("div#temp"));
        $(this).parent().find("form").submit();
    });

    </script>
    
    
<?php    
endif; ?>