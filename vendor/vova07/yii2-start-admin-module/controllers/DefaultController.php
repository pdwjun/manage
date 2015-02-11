<?php

namespace vova07\admin\controllers;

use vova07\admin\components\Controller;
use vova07\rbac\models\Access;
use Yii;

/**
 * Backend default controller.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['error'],
            'roles' => ['@']
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }

    /**
     * Backend main page.
     */
    public function actionIndex()
    {
        $user_id = Yii::$app->user->id;
        $list = Access::getCondomList($user_id);
        if(Yii::$app->user->isGuest)
            return $this->redirect(Yii::$app->urlManager->createUrl('users/guest/login'));

        return $this->render('index',
            [
                'condomlist'=>$list,
            ]);
    }
}
