<!DOCTYPE html>
<html>
<head>
<?=$head?>
</head>
<body>
<?=$banner?>
<div class="container">
<?php foreach ($photos as &$photo): ?>
<div class="list-group-item panel-body">
    <div class="col-xs-12 col-md-12 place-panel-name">
    <div class="row">
    <a class="btn btn-primary btn-lg btn-block" href="<?=base_url($photo['photo_link'])?>">Photo: <?= $photo['photo_link']?></a>
    </div>
    <hr />
    <div class="row">
    <a class="btn btn-default" href="<?=site_url($href['admin']['photos']['pop'].'/'.$place_id.'/'.$photo['id'])?>">
        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
    </a>
    </div>
    </div><!-- place-panel-name -->
</div>
<?php endforeach ?>
<hr />
<?php echo form_open_multipart($href['admin']['photos']['photo']);?>
    <div class="form-group">
    <label for="userfile">Add photo:</label>
    <input type="file" name="userfile" id="userfile"/>
    <hr />
    <input id="submit" name="submit" type="submit" class="btn btn-success" value="Upload" />
    <input type="hidden" name="place_id" id="place_id" value="<?=$place_id?>"/>
    <input type="hidden" name="category_name" id="category_name" value="<?=$category_name?>"/>
    <hr />
    <ul>
    <li><h4 class="help-block">Please keep images under 900kB</p></li>
    <li><h4 class="help-block">Image resolution must be below 960x960</p></li>
    <li><h4 class="help-block">Images must be in jpg or png format</p></li>
    </ul>
    </div><!-- Name -->
</form>
</div><!-- /.container -->
<?=$navbar?>
<?=$js?>
</body>
</html>
