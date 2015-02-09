<?php

/**
 * Backend main page view.
 *
 * @var yii\base\View $this View
 */

use vova07\admin\Module;
use vova07\rbac\models\Condom;
use yii\helpers\Html;

$this->title = Module::t('admin', 'INDEX_TITLE');
$this->params['subtitle'] = Module::t('admin', 'INDEX_SUBTITLE'); ?>
<div class="jumbotron text-center">
<!--  <h1>--><?php //echo Html::encode($this->title); ?><!--</h1>-->
<!--  <p>--><?//= Module::t('admin', 'INDEX_JUMBOTRON_MSG') ?><!--</p>-->
  <?
  foreach($condomlist as $item){
    $condom = Condom::getParams($item['condom_id']);
    ?>
    <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3>
          <?= $condom['company']?>&nbsp;
        </h3>
        <p>
          <?= $condom['dbname']?>&nbsp;
        </p>
      </div>
      <a href="http://<?= $condom['dbname']?>.<?= DOMAIN?>" target="_blank" class="small-box-footer" >
        点击进入 <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
  <?
  }
  ?>
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-teal">
      <div class="inner">
        <h3>
          &nbsp;
        </h3>
        <p>
          &nbsp;
        </p>
      </div>
      <a href="<?= Yii::$app->urlManager->createUrl('blogs/default/create')?>" target="_blank" class="small-box-footer" >
        添加账套 <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
</div>