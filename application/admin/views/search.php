<!DOCTYPE html>
<html>
<head>
<?=$head?>
</head>
<body>
<?=$banner?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-dm-12">
            <?=form_open(current_url(), array('method' => 'get', 'class' => 'form-horizontal', 'role' => 'form'))?>
                <div class="input-group">
                    <input name="search" class="text form-control" value="<?=$this->input->get('search')?>" type="search" placeholder="Search...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-search">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div><!-- /.input-group -->
            </form>
        </div>
    </div><!-- /.row -->
    
    <!-- start showing results -->
    <!-- TODO: implement jQuery lightweight collapse -->
    <!-- TODO: implement paging for search -->
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$category['display_name']?></h3>
            </div><!-- /.panel-heading -->
            <div class="list-group">
                <?php foreach ($places as $place): ?>
                <a class="list-group-item" href="<?=site_url($href['places']['details'].'/'.$category['id'].'/'.$place['id'])?>">
                    <div class="row search-panel">
                        <div class="col-xs-3 col-sm-2 col-md-2">
                            <img class="img-thumbnail place-panel-img" src="<?=base_url('public/images/places/thumbnails/Merdeka_Palace_Hotel_thumb.png')?>" alt="<?=$place['name']?>">
                        </div><!-- col-xs-3 -->
                        <div class="col-xs-9 col-sm-8 col-md-8 place-panel-detail">
                            <div class="row">
                                <div class="col-xs-12 col-md-12 place-panel-name">
                                    <h4><?=$place['name']?></h4>
                                </div><!-- place-panel-name -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-xs-12 col-md-12 place-panel-rating">
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
                        <div class="hidden-xs col-sm-2 col-md-2">
                            <h1><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></h1>
                        </div><!-- /.hidden-xs -->
                    </div><!-- /.search-panel -->
                </a>
                <?php endforeach ?>
            </div><!-- /.list-group -->
        </div><!-- /.panel-primary -->    
    </div><!-- /.row -->
</div><!-- /.container -->
<?=$navbar?>
<?=$js?>
</body>
</html>