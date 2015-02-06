<?php

namespace vova07\rbac\models;

use vova07\users\models\Profile;
use vova07\users\helpers\Security;
use vova07\users\models\User;
use vova07\users\Module;
use vova07\users\traits\ModuleTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
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
 * @property integer $status_id Status
 * @property integer $created_at Created time
 * @property integer $updated_at Updated time
 *
 * @property Profile $profile Profile
 */
class Condom extends ActiveRecord implements IdentityInterface
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

    /**
     * Default role
     */
    const ROLE_DEFAULT = 'user';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%condom}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
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
        return new AccessQuery(get_called_class());
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
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getRoles()
    {
//        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    /**
     * @return array Role array.
     */
    public static function getRoleArray()
    {
        return ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
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
    public static function getParams($id){
        if($id=="")
            return "";
        $con = Yii::$app->db;
        $sql = 'select * from '. Condom::tableName(). ' where id='. $id;
        $param = $con->createCommand($sql)->queryAll();
        if(!empty($param))
            return $param[0];
        else
            return "";

    }
    public static function getCondomList($user_id){
        if($user_id=="")
            return  "";
        $con = Yii::$app->db;
        $sql = 'select * from '. Access::tableName(). ' where user_id='. $user_id;
        $list = $con->createCommand($sql)->queryAll();
        if(!empty($list))
            return $list;
        else
            return "";

    }
    public function addNew($dbname,$company){
        $con = Yii::$app->db;
        $sql = 'insert into '. $this->tableName(). '(id,dbname,company)value("","'.$dbname.'","'.$company.'")';

        if($con->createCommand($sql)->execute())
            return Yii::$app->db->lastInsertID;
        else
            return false;
    }
}
