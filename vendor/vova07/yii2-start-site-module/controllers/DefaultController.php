<?php

namespace vova07\site\controllers;

use vova07\site\components\Controller;
use vova07\site\models\ContactForm;
use vova07\site\Module;
use vova07\users\models\User;
use yii\captcha\CaptchaAction;
use yii\web\ErrorAction;
use yii\web\ViewAction;
use Yii;

/**
 * Frontend main controller.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::className()
            ],
            'about' => [
                'class' => ViewAction::className(),
                'defaultView' => 'about'
            ],
            'captcha' => [
                'class' => CaptchaAction::className(),
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor' => 0XF5F5F5,
                'height' => 34
            ]
        ];
    }

    /**
     * Site "Home" page.
     */
    public function actionIndex()
    {
        $this->layout = '//home';
        $user_id = Yii::$app->getUser()->id;

        $param = array('url' => User::getNameByID($user_id));

        return $this->render('index',$param);

//        if (!Yii::$app->user->isGuest) {
//
//            return Yii::$app->getResponse()->redirect('/frontend/web/index.php?r=users/guest/login');
//            $this->goHome();
//        }
//        else
//        return Yii::$app->getResponse()->redirect('/frontend/web/index.php?r=users/guest/login');
    }

    /**
     * Site "Contact" page.
     */
    public function actionContacts()
    {
        $model = new ContactForm;
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash(
                'success',
                Module::t('site', 'CONTACTS_FLASH_SUCCESS_MSG')
            );
            return $this->refresh();
        } else {
            return $this->render(
                'contacts',
                [
                    'model' => $model
                ]
            );
        }
    }
}
