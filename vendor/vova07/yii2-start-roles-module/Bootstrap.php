<?php

namespace vova07\roles;

use yii\base\BootstrapInterface;

/**
 * roles module bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->urlManager->addRules(
            [
                '<_a:(login|signup|activation|recovery|recovery-confirmation|resend|fileapi-upload)>' => 'roles/guest/<_a>',
                '<_a:logout>' => 'roles/user/<_a>',
                '<_a:email>' => 'roles/default/<_a>',
                'my/settings/<_a:[\w\-]+>' => 'roles/user/<_a>',
            ],
            false
        );

        // Add module I18N category.
        if (!isset($app->i18n->translations['vova07/roles']) && !isset($app->i18n->translations['vova07/*'])) {
            $app->i18n->translations['vova07/roles'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vova07/roles/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'vova07/roles' => 'roles.php',
                ]
            ];
        }
    }
}
