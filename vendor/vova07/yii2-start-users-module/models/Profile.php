<?php

namespace vova07\users\models;

use vova07\fileapi\behaviors\UploadBehavior;
use vova07\users\Module;
use vova07\users\traits\ModuleTrait;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class Profile
 * @package vova07\users\models
 * User profile model.
 *
 * @property integer $user_id User ID
 * @property string $name Name
 * @property string $surname Surname
 *
 * @property User $user User
 */
class Profile extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profiles}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'uploadBehavior' => [
                'class' => UploadBehavior::className(),
                'attributes' => [
                    'avatar_url' => [
                        'path' => $this->module->avatarPath,
                        'tempPath' => $this->module->avatarsTempPath,
                        'url' => $this->module->avatarUrl
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findByUserId($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    /**
     * @return string User full name
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Name
//            ['name', 'match', 'pattern' => '/^[a-zа-яё]+$/iu'],
        //真实姓名 中英文，数字
            ['name', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9_]+$/iu'],
            // Surname
            ['surname', 'match', 'pattern' => '/^[a-zа-яё]+(-[a-zа-яё]+)?$/iu'],
            ['phone', 'unique'],
            ['phone', 'string', 'max'=>11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Module::t('users', 'ATTR_NAME'),
            'surname' => Module::t('users', 'ATTR_SURNAME'),
            'phone' => Module::t('users', 'ATTR_PHONE'),
            'condom' => Module::t('users', 'ATTR_CONDOM')
        ];
    }

    /**
     * @return Profile|null Profile user
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->inverseOf('profile');
    }
}
