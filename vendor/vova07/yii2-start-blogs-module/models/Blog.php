<?php

namespace vova07\blogs\models;

use vova07\base\behaviors\PurifierBehavior;
use vova07\blogs\Module;
use vova07\blogs\traits\ModuleTrait;
use common\account;
use vova07\fileapi\behaviors\UploadBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii;

/**
 * Class Blog
 * @package vova07\blogs\models
 * Blog model.
 *
 * @property integer $id ID
 * @property string $dbname Title //@property string $title Title
 * @property string $company Alias //@property string $alias Alias
 * @property string $address Intro text //@property string $snippet Intro text
 * @property string $note Content //@property string $content Content
 * @property integer $cuser Views //@property integer $views Views
 * @property integer $cphone Views //@property integer $views Views
 * @property integer $status Status //@property integer $status_id Status
 * @property integer $starttime Created time //@property integer $created_at Created time
 * @property integer $created_at Created time
 * @property integer $updated_at Updated time
 */
class Blog extends ActiveRecord
{
    use ModuleTrait;

    /** Unpublished status **/
    const STATUS_UNPUBLISHED = 0;
    /** Published status **/
    const STATUS_PUBLISHED = 1;

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
    public static function find()
    {
        return new BlogQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
            ],
            'sluggableBehavior' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'dbname',
                'slugAttribute' => 'company'
            ],
//            'uploadBehavior' => [
//                'class' => UploadBehavior::className(),
//                'attributes' => [
//                    'preview_url' => [
//                        'path' => $this->module->previewPath,
//                        'tempPath' => $this->module->imagesTempPath,
//                        'url' => $this->module->previewUrl
//                    ],
//                    'image_url' => [
//                        'path' => $this->module->imagePath,
//                        'tempPath' => $this->module->imagesTempPath,
//                        'url' => $this->module->imageUrl
//                    ]
//                ]
//            ],
            'purifierBehavior' => [
                'class' => PurifierBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => [
                        'address',
                        'content' => [
                            'HTML.AllowedElements' => '',
                            'AutoFormat.RemoveEmpty' => true
                        ]
                    ]
                ],
                'textAttributes' => [
                    self::EVENT_BEFORE_VALIDATE => ['dbname', 'company']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Required
            [['dbname', 'company'], 'required'],
            // Trim
            [['dbname', 'company', 'note'], 'trim'],
            [['dbname'], 'unique'],
            // Status
            ['dbname', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/'],
            [
                'status',
                'default',
//                'value' => $this->module->moderation ? self::STATUS_PUBLISHED : self::STATUS_UNPUBLISHED
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('blogs', 'ATTR_ID'),
            'dbname' => Module::t('blogs', 'ATTR_TITLE'),
            'company' => Module::t('blogs', 'ATTR_ALIAS'),
            'address' => Module::t('blogs', 'ATTR_SNIPPET'),
            'note' => Module::t('blogs', 'ATTR_CONTENT'),
            'cuser' => Module::t('blogs', 'ATTR_CUSER'),
            'cphone' => Module::t('blogs', 'ATTR_CPHONE'),
            'status' => Module::t('blogs', 'ATTR_STATUS'),
            'starttime' => Module::t('blogs', 'ATTR_STARTTIME'),
            'created_at' => Module::t('blogs', 'ATTR_CREATED'),
            'updated_at' => Module::t('blogs', 'ATTR_UPDATED'),
//            'preview_url' => Module::t('blogs', 'ATTR_PREVIEW_URL'),
//            'image_url' => Module::t('blogs', 'ATTR_IMAGE_URL'),
        ];
    }

    /**
     * @param $dbname
     * @return bool 创建数据库(账套)
     */
    public function createDb($dbname){
        if(!isset($dbname))
            $dbname = 'test23';
        $sql = "create database account_". $dbname. " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci; use account_". $dbname. "; ";

        $myfile = fopen("../../common/config/create.txt", "r") or die("Unable to open file!");
        $sql .=  fread($myfile,filesize('../../common/config/create.txt'));
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        if($command->execute()){
            $connection->close();
            $this->backDB();
            return true;
        }
        else{
            $connection->close();
            $this->backDB();
            return false;
        }
    }
    protected function backDB(){

        //反正当前数据库
        $connection = Yii::$app->db;
        $use = "use `". account::getDbName(). "`;";
        $command = $connection->createCommand($use);
        $command->execute();
        $connection->close();
    }

    public function addCondom($load){

    }

}
