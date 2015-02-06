<?php

/**
 * User form view.
 *
 * @var \yii\web\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var \vova07\users\models\backend\User $model Model
 * @var \vova07\users\models\Profile $profile Profile
 * @var array $roleArray Roles array
 * @var array $statusArray Statuses array
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 */

use vova07\fileapi\Widget;
use vova07\users\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vova07\rbac\models\Access;

?>
<?php $form = ActiveForm::begin(); ?>
<?php $box->beginBody(); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($profile, 'name') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($profile, 'surname') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($user, 'username') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($user, 'email') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($user, 'password')->passwordInput() ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($user, 'repassword')->passwordInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?=
            $form->field($user, 'status_id')->dropDownList(
                $statusArray,
                [
                    'prompt' => Module::t('users', 'BACKEND_PROMPT_STATUS')
                ]
            ) ?>
        </div>
        <div class="col-sm-6">
            <div class="form-group field-user-status_id">
                <label class="control-label" for="user-status_id">账套</label>
                <?
                $list = Access::getCondomList($user->id);
                foreach($list as $item){
                    $select[] = $item['condom_id'];
                }
                $list = Access::getCondomList(Yii::$app->getUser()->id);
                foreach ($list as $item) {
                    $items[$item['condom_id']] = $item['dbname'].'/'.$item['company'];
                }


                echo HTML::dropDownList(
                    'condom',
                    $select,
                    $items,
                    [
                        'class'=>'form-control',
                        'multiple' => true,     //账套应该可以多选
                        'placeholder' => Module::t('rbac', 'BACKEND_ROLES_PERMISSIONS_PROMPT')
                    ]
                    )
                //id="user-status_id" class="form-control" name="User[status_id]">
                ?>
            </div>
        </div>
            <?
            if(in_array(Yii::$app->getUser()->id,Yii::$app->params['superadmin'])){
                ?>
            <div class="col-sm-6">
                <?

                    echo $form->field($user, 'vip')->dropDownList(
                        $vipArray,
                        [
                            'prompt' => '选择'
                        ]);
                ?>
            </div>
            <?
            }
        ?>
<!--        <div class="col-sm-6">-->
<!--            --><?//=
//            $form->field($user, 'role')->dropDownList(
//                $roleArray,
//                [
//                    'prompt' => Module::t('users', 'BACKEND_PROMPT_ROLE')
//                ]
//            ) ?>
<!--        </div>-->
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($profile, 'avatar_url')->widget(Widget::className(),
                [
                    'settings' => [
                        'url' => ['fileapi-upload']
                    ],
                    'crop' => true,
                    'cropResizeWidth' => 100,
                    'cropResizeHeight' => 100
                ]
            ) ?>
        </div>
    </div>
<?php $box->endBody(); ?>
<?php $box->beginFooter(); ?>
<?= Html::submitButton(
    $user->isNewRecord ? Module::t('users', 'BACKEND_CREATE_SUBMIT') : Module::t('users', 'BACKEND_UPDATE_SUBMIT'),
    [
        'class' => $user->isNewRecord ? 'btn btn-primary btn-large' : 'btn btn-success btn-large'
    ]
) ?>
<?php $box->endFooter(); ?>
<?php ActiveForm::end(); ?>