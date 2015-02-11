<?php

/**
 * Frontend main page view.
 *
 * @var yii\web\View $this View
 */

$this->title = Yii::$app->name;
$this->params['noTitle'] = true; ?>

<section id="main-slider" class="no-margin center">
    <div class="well">
        <p><img src="<?= $this->assetManager->publish('@vova07/themes/site/images/slider/bg2.png')[1] ?>" alt="老法师" /></p>
        <div class="col-md-12">
            <a href="/backend" class="btn btn-primary btn-lg" >登陆后台</a>
        </div>
    </div>
</section>

<section id="services" class="emerald">
    <div class="container">

    </div>
</section>