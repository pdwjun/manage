<?php

namespace vova07\users\models;

use vova07\users\helpers\Security;
use vova07\users\models\backend\UserSearch;
use vova07\users\Module;
use vova07\users\traits\ModuleTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use Yii;

/**
 * Class User
 * @package vova07\users\models
 * User model.
 *
 * @property integer $id ID
 * @property string $username Username
 * @property string $email E-mail
 * @property string $password_hash Password hash
 * @property string $auth_key Authentication key
 * @property string $role Role
 * @property string $group Group
 * @property integer $status_id Status
 * @property integer $vip VIP
 * @property integer $created_at Created time
 * @property integer $updated_at Updated time
 *
 * @property Profile $profile Profile
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    /** Inactive status */
    const STATUS_INACTIVE = 0;
    /** Active status */
    const STATUS_ACTIVE = 1;
    /** Banned status */
    const STATUS_BANNED = 2;
    /** Deleted status */
    const STATUS_DELETED = 3;

    /** Inactive status */
    const VIP_NOT = 0;
    /** Active status */
    const VIP_YES = 1;
    /**
     * Default role
     */
    const ROLE_DEFAULT = 'superadmin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @return array Status array.
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => Module::t('users', 'STATUS_ACTIVE'),
            self::STATUS_INACTIVE => Module::t('users', 'STATUS_INACTIVE'),
            self::STATUS_BANNED => Module::t('users', 'STATUS_BANNED')
        ];
    }

    /**
     * @return array Role array.
     */
    public static function getRoleArray()
    {
        return ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Find users by IDs.
     *
     * @param $ids Users IDs
     * @param null $scope Scope
     *
     * @return array|\yii\db\ActiveRecord[] Users
     */
    public static function findIdentities($ids, $scope = null)
    {
        $query = static::find()->where(['id' => $ids]);
        if ($scope !== null) {
            if (is_array($scope)) {
                foreach ($scope as $value) {
                    $query->$value();
                }
            } else {
                $query->$scope();
            }
        }
        return $query->all();
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Find model by username.
     *
     * @param string $username Username
     * @param string $scope Scope
     *
     * @return array|\yii\db\ActiveRecord[] User
     */
    public static function findByUsername($username, $scope = null)
    {
        $query = static::find()->where(['username' => $username]);
        if ($scope !== null) {
            if (is_array($scope)) {
                foreach ($scope as $value) {
                    $query->$value();
                }
            } else {
                $query->$scope();
            }
        }
        return $query->one();
    }

    /**
     * Find model by email.
     *
     * @param string $email Email
     * @param string $scope Scope
     *
     * @return array|\yii\db\ActiveRecord[] User
     */
    public static function findByEmail($email, $scope = null)
    {
        $query = static::find()->where(['email' => $email]);
        if ($scope !== null) {
            if (is_array($scope)) {
                foreach ($scope as $value) {
                    $query->$value();
                }
            } else {
                $query->$scope();
            }
        }
        return $query->one();
    }

    /**
     * Find model by token.
     *
     * @param string $token Token
     * @param string $scope Scope
     *
     * @return array|\yii\db\ActiveRecord[] User
     */
    public static function findByToken($token, $scope = null)
    {
        $query = static::find()->where(['token' => $token]);
        if ($scope !== null) {
            if (is_array($scope)) {
                foreach ($scope as $value) {
                    $query->$value();
                }
            } else {
                $query->$scope();
            }
        }
        return $query->one();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Auth Key validation.
     *
     * @param string $authKey
     *
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Password validation.
     *
     * @param string $password
     *
     * @return boolean
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @return string Human readable created date
     */
    public function getCreated()
    {
        return Yii::$app->formatter->asDate($this->created_at);
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Module::t('users', 'ATTR_USERNAME'),
            'email' => Module::t('users', 'ATTR_EMAIL'),
            'role' => Module::t('users', 'ATTR_ROLE'),
            'status_id' => Module::t('users', 'ATTR_STATUS'),
            'vip' => Module::t('users', 'ATTR_VIP'),
            'created_at' => Module::t('users', 'ATTR_CREATED'),
            'updated_at' => Module::t('users', 'ATTR_UPDATED'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                // Set default status
                if (!$this->status_id) {
                    $this->status_id = $this->module->requireEmailConfirmation ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;
                }
                // Set default role
                if (!$this->role) {
                    $this->role = self::ROLE_DEFAULT;
                }
                // Generate auth and secure keys
                $this->generateAuthKey();
                $this->generateToken();
            }
            return true;
        }
        return false;
    }

    /**
     * @return Profile|null User profile
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Generates "remember me" authentication key.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates secure key.
     */
    public function generateToken()
    {
        $this->token = Security::generateExpiringRandomString();
    }

    /**
     * Activates user account.
     *
     * @return boolean true if account was successfully activated
     */
    public function activation()
    {
        $this->status_id = self::STATUS_ACTIVE;
        $this->generateToken();
        return $this->save(false);
    }

    /**
     * Recover password.
     *
     * @param string $password New Password
     *
     * @return boolean true if password was successfully recovered
     */
    public function recovery($password)
    {
        $this->setPassword($password);
        $this->generateToken();
        return $this->save(false);
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Change user password.
     *
     * @param string $password Password
     *
     * @return boolean true if password was successfully changed
     */
    public function password($password)
    {
        $this->setPassword($password);
        return $this->save(false);
    }
    public static function getNameByID($id){
        if($id=='')
            return "";
        $connection = Yii::$app->db;
        $sql = 'select username from `yii2_start_users` where id='. $id;
        $list = $connection->createCommand($sql)->queryAll();
        if($list)
            return $list[0]['username'];
        else
            return "";
    }

    //设置组
    public static function setGroup(){
        $user_id = Yii::$app->db->lastInsertID;
        $user = self::findIdentity($user_id);
        $login = Yii::$app->user->isGuest;
        if(!$login){
            $group_id = Yii::$app->getUser()->identity->group;
            $user->group = $group_id;
        }
        else
            $user->group = $user_id;
        $user->save();
    }

    public static function status($status_id)
    {
        $result = '';
        switch($status_id){
            case 0 :
                $result = Module::t('users', 'STATUS_INACTIVE');break;
            case 1 :
                $result = Module::t('users', 'STATUS_ACTIVE');break;
            case 2 :
                $result = Module::t('users', 'STATUS_BANNED');break;
        }

        return $result;
    }
    public static function vip($vip)
    {
        return $vip==1?'VIP':'普通用户';
    }

}
