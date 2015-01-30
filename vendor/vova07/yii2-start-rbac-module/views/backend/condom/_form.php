<?php

/**
 * Role form view.
 *
 * @var \yii\base\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var \yii\base\DynamicModel $model Model
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 * @var array $roleArray Roles array
 * @var array $ruleArray Rules array
 * @var array $permissionArray Permissions array
 */

use vova07\blogs\Module;
use vova07\select2\Widget;
use yii\helpers\Html;
use vova07\users\models\User;
use vova07\rbac\models\Access;
use yii\widgets\ActiveForm;

?>
<?php
if($access_id!="")
    $access_param = Access::getParams($access_id);
$role_id = $access_param['role_id'];
$user_id = $access_param['user_id'];
?>
<?php $form = ActiveForm::begin(); ?>
<?php $box->beginBody(); ?>
    <div class="row">
        <input type="hidden" name="Role[id]" value="<?= $access_id ?>"/>
        <div class="col-sm-6">
        <?= Html::activeLabel($model, 'name'); ?>:
        <?= User::getNameByID($user_id)?>
        </div>
        <div class="col-sm-6">
        <?= Html::activeLabel($model, 'role_id'); ?>:
            <?
                Widget::begin(
                    ['name' => 'Role[role_id]',
                        'value' => $role_id,
                        'items' => $roleArray,
                        'options' => [
                        'prompt' => Module::t('rbac', 'BACKEND_ROLES_RULE_NAME_PROMPT'),
                    ],
                    'settings' => [
                        'width' => '100%',
                    ]]);
                Widget::end();

            ?>
<!--            --><?//= $form->field($model, 'role_id')->widget(Widget::className(), [
//    'options' => [
//        'prompt' => Module::t('rbac', 'BACKEND_ROLES_RULE_NAME_PROMPT'),
//    ],
//    'settings' => [
//        'width' => '100%',
//    ],
//    'value' => $role_id,
//    'items' => $roleArray
//])
?>
        </div>
    </div>
<!--    <div class="row">-->
<!--        <div class="col-sm-6">-->
<!--            --><? //= $form->field($model, 'description')->textarea() ?>
<!--        </div>-->
<!--        <div class="col-sm-6">-->
<!--            --><? //= $form->field($model, 'data')->textarea() ?>
<!--        </div>-->
<!--    </div>-->
<!--    <div class="row">-->
<!--        <div class="col-sm-6">-->
<!--            --><? //= $form->field($model, 'rolesChildren')->widget(Widget::className(), [
//                'options' => [
//                    'multiple' => true,
//                    'placeholder' => Module::t('rbac', 'BACKEND_ROLES_ROLES_PROMPT')
//                ],
//                'settings' => [
//                    'width' => '100%',
//                ],
//                'items' => $roleArray
//            ]) ?>
<!--        </div>-->
<!--        <div class="col-sm-6">-->
<!--            --><? //= $form->field($model, 'permissionsChildren')->widget(Widget::className(), [
//                'options' => [
//                    'multiple' => true,
//                    'placeholder' => Module::t('rbac', 'BACKEND_ROLES_PERMISSIONS_PROMPT')
//                ],
//                'settings' => [
//                    'width' => '100%',
//                ],
//                'items' => $permissionArray
//            ]) ?>
<!--        </div>-->
    </div>
<?php $box->endBody(); ?>
<?php $box->beginFooter(); ?>
<?= Html::submitButton(!isset($update) ? Module::t('rbac', 'BACKEND_ROLES_CREATE_SUBMIT') : Module::t('rbac', 'BACKEND_ROLES_UPDATE_SUBMIT'), [
    'class' => !isset($update) ? 'btn btn-primary btn-large' : 'btn btn-success btn-large'
]) ?>
<?php $box->endFooter(); ?>
<?php ActiveForm::end(); ?>