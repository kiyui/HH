<!DOCTYPE html>
<html>
<head>
    <?=$head?>
</head>
<body>
<?=$banner?>
<div class="category-title bg-primary">
    <div class="bg-primary"><?=$category['display_name']?></div>
    <?php if ($src['category_icon'] !== FALSE): ?>
    <img class="img-thumbnail" alt="<?=$category['display_name']?>" src="<?=base_url($src['category_icon'])?>">
    <?php endif?>
</div><!-- /.category-title -->
<div class="container">
    <!-- Start listing places -->
    <?php foreach ($places as $place): ?>
    <div class="row place-panel">
        <a class="" href="<?=site_url($href['places']['details'].'/'.$category['id'].'/'.$place['id'])?>">
            <div class="col-xs-3 col-sm-2 col-md-2">
                <img class="img-thumbnail place-panel-img" src="<?=base_url($thumbnails[$category['id']])?>" alt="<?=$place['name']?>">
            </div><!-- col-xs-3 -->
            <div class="col-xs-9 col-sm-9 col-md-9 place-panel-detail">
                <div class="row">
                    <div class="col-xs-12 col-md-12 place-panel-name">
                        <?=$place['name']?>
                    </div><!-- place-panel-name -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <h4>
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?php if ($place['rating'] > 0): ?>
                                <span class="glyphicon glyphicon-star"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-star-empty"></span>
                            <?php endif; ?>
                            <?php $place['rating']--; ?>
                        <?php endfor; ?>
                        </h4>
                    </div><!-- /.col-xs-12 -->
                </div><!-- /.row -->
            </div><!-- /. place-panel-detail -->
            <div class="hidden-xs col-sm-1 col-md-1">
                <h1><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></h1>
            </div><!-- /.hidden-xs -->
        </a>
    </div><!-- /.row -->
    <?php endforeach ?>
</div><!-- /.container -->
<?=$navbar?>
<?=$js?>
</body>
</html>
