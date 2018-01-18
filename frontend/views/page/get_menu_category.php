<?php
use yii\helpers\Html;

if (count($modelMenuCategory) > 0):
    foreach ($modelMenuCategory as $menuCategoryData): 
        $color = '';
        if (!(empty($menuCategoryData['color'])))
            $color = ';background-color:' . $menuCategoryData['color']; ?>        

        <a href="#" id="menuCategoryId">
            <?= Html::hiddenInput('parentCategoryId', $menuCategoryData['parent_category_id'], ['id' => 'parentCategoryId']) ?>
            <?= Html::hiddenInput('categoryId', $menuCategoryData['id'], ['id' => 'categoryId']) ?>
            
            <div class="col-md-3 col-sm-3 mb">
                <div class="product-panel-2" style="padding: 10px 0 5px 0<?= $color ?>">   
                    <br>
                    <h5 style="color: #000; font-weight: bold"><?= $menuCategoryData['nama_category'] ?></h5>
                    <br>
                </div>
            </div>
        </a>

<?php
    endforeach; 
else: ?>
    <br><br><br><br>
    No Data Found
    <br><br><br><br><br>

<?php    
endif; ?>

<script>                
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
    $("a#menuCategoryId").on("click", function(event) {
        event.preventDefault();     
        
        var thisObj = $(this);                
        var data = {
            "_csrf" : csrfToken,
            "id": $(this).find("input#categoryId").val(),            
        };
        
        
        
        if ($(this).find("input#parentCategoryId").val() == "") {
            
            $.ajax({
                cache: false,
                type: "POST",
                data: data,
                url: "<?= Yii::$app->urlManager->createUrl(['page/get-menu-category']) ?>",
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    thisObj.off("click");
                    
                    $("#menu-container").html(response);
                    $(".overlay").hide();
                    $(".loading-img").hide();                    
                }
            });
        } else {
            $.ajax({
                cache: false,
                type: "POST",
                data: data,
                url: "<?= Yii::$app->urlManager->createUrl(['page/get-menu']) ?>",
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    $("a#btnMenuBack").css("display", "none");
                    
                    $("#menu-container").html(response);
                    $(".overlay").hide();
                    $(".loading-img").hide();                                        
                }
            });            
        }
    });
    
    <?php
    if (!empty($pid)): ?>
        
        $("a#btnMenuBack").css("display", "block");
        
        $("a#btnMenuBack").off("click");        
        $("a#btnMenuBack").on("click", function(event) {
            event.preventDefault();                                 
            
            $.ajax({
                cache: false,
                type: "POST",
                data: {"_csrf" : csrfToken},
                url: "<?= Yii::$app->urlManager->createUrl(['page/get-menu-category']) ?>",
                beforeSend: function(xhr) {
                    $(".overlay").show();
                    $(".loading-img").show();
                },
                success: function(response) {
                    $("a#btnMenuBack").css("display", "none");
                    $("a#btnMenuBack").off("click");
                    
                    $("#menu-container").html(response);
                    $(".overlay").hide();
                    $(".loading-img").hide();
                }
            });
        });
        
    <?php
    endif; ?>
        
</script>


