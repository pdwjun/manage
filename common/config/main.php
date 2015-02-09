<?php

$host = explode('.', $_SERVER["HTTP_HOST"]);
if (count($host) > 2) {
    define('DOMAIN', $host[1] . '.' . $host[2]);
} else {
    define('DOMAIN', $host[0] . '.' . $host[1]);
}
return [
    'id' => 'abc.com',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Europe/Moscow',
    'modules' => [
        'users' => [
            'class' => 'vova07\users\Module',
            'robotEmail' => 'no-reply@domain.com',
            'robotName' => 'Robot'
        ],
        'blogs' => [
            'class' => 'vova07\blogs\Module'
        ],
        'blogss' => [
            'class' => 'vova07\blogs\Module'
        ],
        'comments' => [
            'class' => 'vova07\comments\Module'
        ]
    ],
    'components' => [
        'mailer' => [
          'class' => 'yii\swiftmailer\Mailer',
//            'useFileTransport' => true,
            'viewPath' => '@common/mail',
          'transport' => [
              'class' => 'Swift_SmtpTransport',
//              'host' => 'smtp.gmail.com',
//              'username' => 'pdwjun@gmail.com',
//              'password' => 'usixozyohbjsbisw',
//              'port' => '465',
              'host' => 'smtp.126.com',
              'username' => 'pdwjun@126.com',
              'password' => 'rxz5558290555',
              'port' => '587',
//              'auth_mode' => 'login',
              'encryption' => 'tls',
          ],
        ],
        'user' => [
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity', 'httpOnly' => true, 'domain' => '.' . DOMAIN],
            'class' => 'yii\web\User',
            'identityClass' => 'vova07\users\models\User',
            'loginUrl' => ['/users/guest/login']
        ],
        'session' => [
            'savePath' => '/../sessions',
            'cookieParams' => ['domain' => '.' . DOMAIN, 'lifetime' => 0],
            'timeout' => 3600,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@root/cache',
            'keyPrefix' => 'yii2start'
        ],
//        'urlManager' => [
//            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true,
//            'showScriptName' => false,
//            'suffix' => '/'
//        ],
        'assetManager' => [
            'linkAssets' => true
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => [
                'user'
            ],
            'itemFile' => '@vova07/rbac/data/items.php',
            'assignmentFile' => '@vova07/rbac/data/assignments.php',
            'ruleFile' => '@vova07/rbac/data/rules.php',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.y',
            'datetimeFormat' => 'HH:mm:ss dd.MM.y'
        ],
        'db' => require(__DIR__ . '/db.php')
    ],
    'params' => require(__DIR__ . '/params.php')
];
