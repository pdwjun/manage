<?php

/**
 * Blog update view.
 *
 * @var yii\base\View $this View
 * @var vova07\blogs\models\backend\Blog $model Model
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 * @var \vova07\users\models\backend\UserSearch $searchModel Search model
 * @var array $statusArray Statuses array
 */

use vova07\themes\admin\widgets\Box;
use vova07\blogs\Module;
use vova07\roles\models\Role;
use vova07\themes\admin\widgets\GridView;
use vova07\users\models\backend\AccessSearch;
use yii\helpers\Html;
use yii\grid\CheckboxColumn;


$this->title = Module::t('blogs', 'BACKEND_UPDATE_TITLE');
$this->params['subtitle'] = Module::t('blogs', 'BACKEND_UPDATE_SUBTITLE');
$this->params['breadcrumbs'] = [
    [
        'label' => $this->title,
        'url' => ['index'],
    ],
    $this->params['subtitle']
];
?>
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
    <?php
    $gridId = 'users-grid';
    $gridConfig = [
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => CheckboxColumn::classname()
            ],
            [
                'attribute' => 'id',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a(
                        $model['id'],
                        ['update', ['id' => $model['id'],'type'=> "1",'role_id'=>$model['role_id']]]
                    );
                }
            ],
            [
                'label' => '登陆账号',
                'attribute' => 'user_id',
                'format' => 'html',
                'value' => 'users.username'
            ],
            [
                'label' => '用户名',
                'attribute' => 'user_id',
                'value' => 'profile.name'
            ],
            [
                'label' => '角色名称',
                'attribute' => 'role_id',
                'value' => function($model){

                    $connection = Yii::$app->db;
                    $sql = 'select name from `yii2_start_roles` where id='. $model->role_id;
                    $list = $connection->createCommand($sql)->queryAll();
                    return $list[0]['name'];
                }

            ],
        ]
    ];
    ?>
</div>
<div class="row">

    <?php Box::begin(
        [
            'title' => $this->params['subtitle'],
            'bodyOptions' => [
                'class' => 'table-responsive'
            ],
            'grid' => $gridId
        ]
    ); ?>
    <?= GridView::widget($gridConfig); ?>
    <?php Box::end(); ?>

</div>
