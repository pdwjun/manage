<?php

namespace vova07\blogs\controllers\backend;

use vova07\admin\components\Controller;
use vova07\blogs\models\backend\Blog;
use vova07\blogs\models\backend\BlogSearch;
use vova07\fileapi\actions\UploadAction as FileAPIUpload;
use vova07\imperavi\actions\GetAction as ImperaviGet;
use vova07\imperavi\actions\UploadAction as ImperaviUpload;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Default backend controller.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'] = [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => ['BViewBlogs']
            ]
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['create'],
            'roles' => ['BCreateBlogs']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['update'],
            'roles' => ['BUpdateBlogs']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['delete', 'batch-delete'],
            'roles' => ['BDeleteBlogs']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['imperavi-get', 'imperavi-image-upload', 'imperavi-file-upload', 'fileapi-upload'],
            'roles' => ['BCreateBlogs', 'BUpdateBlogs']
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'create' => ['get', 'post'],
                'update' => ['get', 'put', 'post'],
                'delete' => ['post', 'delete'],
                'batch-delete' => ['post', 'delete']
            ]
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'imperavi-get' => [
                'class' => ImperaviGet::className(),
                'url' => $this->module->contentUrl,
                'path' => $this->module->contentPath
            ],
            'imperavi-image-upload' => [
                'class' => ImperaviUpload::className(),
                'url' => $this->module->contentUrl,
                'path' => $this->module->contentPath
            ],
            'imperavi-file-upload' => [
                'class' => ImperaviUpload::className(),
                'url' => $this->module->fileUrl,
                'path' => $this->module->filePath,
                'uploadOnlyImage' => false
            ],
            'fileapi-upload' => [
                'class' => FileAPIUpload::className(),
                'path' => $this->module->imagesTempPath
            ]
        ];
    }

    /**
     * Posts list page.
     */
    public function actionIndex()
    {
        $searchModel = new BlogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $statusArray = Blog::getStatusArray();

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'statusArray' => $statusArray
            ]);
    }

    /**
     * Create post page.
     */
    public function actionCreate()
    {
        $model = new Blog(['scenario' => 'admin-create']);
        $statusArray = Blog::getStatusArray();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if($model->isNewRecord){
                    $a = $this->createDb($model->dbname);
                    if($a!=true)
                        $this->refresh();
                    //反正当前数据库
                    $connection = Yii::$app->db;
                    $use = "use `". $this->getDbName(). "`;";
                    $command = $connection->createCommand($use);
                    $command->execute();
                }
                if ($model->save(false)) {
                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('blogs', 'BACKEND_FLASH_FAIL_ADMIN_CREATE'));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('create', [
                'model' => $model,
                'statusArray' => $statusArray
            ]);
    }

    /**
     * Update post page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('admin-update');
        $statusArray = Blog::getStatusArray();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->refresh();
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('blogs', 'BACKEND_FLASH_FAIL_ADMIN_UPDATE'));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('update', [
                'model' => $model,
                'statusArray' => $statusArray
            ]);
    }

    /**
     * Delete post page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Delete multiple posts page.
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionBatchDelete()
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids);
            foreach ($models as $model) {
                $model->delete();
            }
            return $this->redirect(['index']);
        } else {
            throw new HttpException(400);
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Post ID
     *
     * @return \vova07\blogs\models\backend\Blog Model
     *
     * @throws HttpException 404 error if post not found
     */
    protected function findModel($id)
    {
        if (is_array($id)) {
            /** @var \vova07\blogs\models\backend\Blog $model */
            $model = Blog::findAll($id);
        } else {
            /** @var \vova07\blogs\models\backend\Blog $model */
            $model = Blog::findOne($id);
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new HttpException(404);
        }
    }

    public function createDb($dbname){
        if(!isset($dbname))
            $dbname = 'test23';
        $sql = "create database account_". $dbname. " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci; use account_". $dbname. "; ";

        $myfile = fopen("../config/create.txt", "r") or die("Unable to open file!");
        $sql .=  fread($myfile,filesize('../config/create.txt'));
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        if($command->execute()){
            return true;
        }
        else
            return false;
    }

    public function getDbName(){
        //数据库 'dsn' => 'mysql:host=127.0.0.1;dbname=yii2-blog',
        $str = Yii::$app->getDb()->dsn;
        preg_match('/dbname=.*/', $str, $dbname);
        if($dbname!="")
            return substr($dbname[0], 7);
        else
            return "";
    }
}
