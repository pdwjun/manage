<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('Create_db') or define('Create_db', false);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/aliases.php');
require(__DIR__ . '/../../common/common.php');

if (false !== strpos('abc.com', $_SERVER['SERVER_NAME'])) {
    defined('Domain') or define('Domain', 'abc.com');
    $config = yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/../../common/config/main-local.php'),
        require(__DIR__ . '/../config/main-local.php')
    );
}else
{
    defined('Domain') or define('Domain', 'sorcerer.com.cn');
    $config = yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/../../common/config/main.php'),
        require(__DIR__ . '/../config/main.php')
    );

}
$application = new yii\web\Application($config);
$application->run();
