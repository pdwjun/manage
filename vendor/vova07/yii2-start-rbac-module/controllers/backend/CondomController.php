<?php

namespace vova07\rbac\controllers\backend;

use vova07\admin\components\Controller;
use vova07\blogs\models\backend\Blog;
use vova07\blogs\models\backend\BlogSearch;
use vova07\rbac\models\Access;
use vova07\rbac\models\AccessSearch;
use vova07\rbac\models\RoleManage;
use vova07\rbac\models\Role;
use vova07\rbac\Module;
use vova07\roles\models\backend\rolesearch;
use vova07\users\models\backend\UserSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\User;

/**
 * Roles controller.
 */
class CondomController extends Controller
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
                'roles' => ['BViewRoles']
            ]
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['create'],
            'roles' => ['BCreateRoles']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['update'],
            'roles' => ['BUpdateRoles']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['delete', 'batch-delete'],
            'roles' => ['BDeleteRoles']
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
     * Roles list page.
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
     * Create role page.
     */
    public function actionCreate()
    {
        $model = new Role(['scenario' => 'admin-create']);
        $roleArray = ArrayHelper::map($model->roles, 'name', 'name');
        $ruleArray = ArrayHelper::map($model->rules, 'name', 'name');
        $permissionArray = ArrayHelper::map($model->permissions, 'name', 'name');

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->add()) {
                    return $this->redirect(['update', 'name' => $model->name]);
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('rbac', 'BACKEND_ROLES_FLASH_FAIL_ADMIN_CREATE'));
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $model->getErrors();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'roleArray' => $roleArray,
            'ruleArray' => $ruleArray,
            'permissionArray' => $permissionArray,
        ]);
    }

    /**
     * Update role page.
     *
     * @param string $name Role name
     *
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = $_REQUEST[1]['id'];
        $type = $_REQUEST[1]['type'];
        if($type=='1'&&isset($_REQUEST[1]['role_id'])){
//            $this->editCondom($id,$_REQUEST[1]['role_id']);
            $role_id = $_REQUEST[1]['role_id'];
        $name = $this->getNameByID($role_id);
        $model = Role::findIdentity($name);
        $model->setScenario('admin-update');
        $statusArray = Blog::getStatusArray();
//        $roleArray = ArrayHelper::map($model->roles, 'name', 'name');
            //账套下的角色列表
        $roleArray = $this->getRoleArray(1);
        $ruleArray = ArrayHelper::map($model->rules, 'name', 'name');
        $permissionArray = ArrayHelper::map($model->permissions, 'name', 'name');
        if ($model->load(Yii::$app->request->post())) {
            //保存access权限数据
//            $role_id = $_POST['Role']['role_id'];
//            $name = $_POST['Role']['name'];
            if($this->saveAccess())
                return $this->refresh();
            else
//                Yii::$app->session->setFlash('danger', Module::t('blogs', 'BACKEND_FLASH_FAIL_ADMIN_UPDATE'));
                return $this->refresh();
//            $id =
//            if ($model->validate()) {
//            if ($model->save(false)) {
//                return $this->refresh();
//            } else {
//                Yii::$app->session->setFlash('danger', Module::t('blogs', 'BACKEND_FLASH_FAIL_ADMIN_UPDATE'));
//                return $this->refresh();
//            }
//            } elseif (Yii::$app->request->isAjax) {
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return ActiveForm::validate($model);
//            }
        }

        return $this->render('edit', [
            'access_id' => $id,
            'model' => $model,
            'role_id' => $role_id,
            'roleArray' => $roleArray,
            'ruleArray' => $ruleArray,
            'permissionArray' => $permissionArray,
            'statusArray' => $statusArray
        ]);}
        else {
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

            $users = Module::getRoles('account');
            $searchModel = new AccessSearch();
            $dataProvider = $searchModel->searchByCondom($id);
            return $this->render('update', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'users' => $users,
                'statusArray' => $statusArray
            ]);
        }
    }

    /**
     * Update post page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function editCondom($id,$role_id)
    {
    }
    /**
     * Delete role page.
     *
     * @param string $name Role name
     *
     * @return mixed
     */
    public function actionDelete($name)
    {
        $model = $this->findRole($name);

        if (!Yii::$app->authManager->remove($model)) {
            Yii::$app->session->setFlash('danger', Module::t('rbac', 'BACKEND_ROLES_FLASH_FAIL_ADMIN_DELETE'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Delete multiple roles page.
     *
     * @return mixed
     * @throws \yii\web\HttpException 400 if request is invalid
     */
    public function actionBatchDelete()
    {
        if (($names = Yii::$app->request->post('names')) !== null) {
            $auth = Yii::$app->authManager;
            foreach ($names as $item) {
                $role = $this->findRole($item['name']);
                $auth->remove($role);
            }
            return $this->redirect(['index']);
        } else {
            throw new BadRequestHttpException('BACKEND_ROLES_ONLY_POST_IS_ALLOWED');
        }
    }

    /**
     * Find role by name.
     *
     * @param string $name Role name
     *
     * @return \yii\rbac\Role Role
     *
     * @throws HttpException 404 error if role not found
     */
    protected function findRole($name)
    {
        if (($model = Yii::$app->authManager->getRole($name)) !== null) {
            return $model;
        } else {
            throw new HttpException(404, Module::t('rbac', 'BACKEND_ROLES_NOT_FOUND'));
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

    public static function getNameByID($role_id){

        $connection = Yii::$app->db;
        $sql = 'select name from `yii2_start_roles` where id='. $role_id;
        $list = $connection->createCommand($sql)->queryAll();
        if($list)
            return $list[0]['name'];
        else
            return "";
    }

    public static function getRoleArray($condom_id){
        $connection = Yii::$app->db;
        $sql = 'select * from `yii2_start_roles` where condom_id='. $condom_id;
        $list = $connection->createCommand($sql)->queryAll();
        $roleArray = array();
        if(!empty($list))
        {
            foreach ($list as $item) {
                $roleArray[$item['id']] = $item['name'];
            }
        }
        return $roleArray;
    }

    public function saveAccess(){
        $access = $_REQUEST['Role'];
        $connection = Yii::$app->db;
        $sql = 'update `yii2_start_access` set role_id='. $access['role_id']. ' where id='. $access['id'];
        $result = $connection->createCommand($sql)->execute();
        if($result)
            return true;
        else
            return false;
    }
}
