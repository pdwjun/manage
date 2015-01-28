<?php

namespace vova07\admin\components;

use yii\filters\AccessControl;

/**
 * Main backend controller.
 */
class Controller extends \yii\web\Controller
{

    //本地测试修改hosts后，起初正常，过段时间，可能半天 1天之后，不知道是框架bug 还是 vova07 module bug，会造成无法登陆
    //此设置可为 跨站请求伪造(CSRF)防护
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['accessBackend']
                    ]
                ]
            ]
        ];
    }
}
