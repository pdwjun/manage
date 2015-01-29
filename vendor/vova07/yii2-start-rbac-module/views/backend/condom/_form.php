<?php

/**
 * Blog form view.
 *
 * @var \yii\base\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var \vova07\blogs\models\backend\Blog $model Model
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 * @var array $statusArray Statuses array
 */

use vova07\blogs\Module;
use vova07\fileapi\Widget as FileAPI;
use vova07\imperavi\Widget as Imperavi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

?>
<?php $box->beginBody(); ?>
    <div class="row">
        <div class="col-sm-2">
            <?= Html::activeLabel($model, 'dbname'); ?>:
            <?= $model->dbname ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeLabel($model, 'company'); ?>:
            <?= $model->company ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeLabel($model, 'cuser'); ?>:
            <?= $model->cuser ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeLabel($model, 'cphone'); ?>:
            <?= $model->cphone ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeLabel($model, 'status'); ?>:
            <?= Module::t('blogs', $model->status ? "STATUS_PUBLISHED" : "STATUS_UNPUBLISHED") ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
        </div>
    </div>
<?php $box->endBody(); ?>
<?php $box->beginFooter(); ?>
<?php $box->endFooter(); ?>