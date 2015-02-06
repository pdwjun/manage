<?php
/**
 * Created by PhpStorm.
 * User: pdwjun
 * Date: 2015/2/2
 * Time: 13:50
 */
namespace common;

use vova07\users\models\frontend\User;
use yii\base\Model;
use yii;

class account extends Model{

    /**
     *  当前数据库名字
     */
    public static function getDbName(){
        //数据库 'dsn' => 'mysql:host=127.0.0.1;dbname=yii2-blog',
        $str = Yii::$app->getDb()->dsn;
        preg_match('/dbname=.*/', $str, $dbname);
        if($dbname!="")
            return substr($dbname[0], 7);
        else
            return "";
    }

    /**
     * 是否超级管理员
     */
    public static function checkSuperAdmin(){
        return in_array(Yii::$app->getUser()->id,Yii::$app->params['superadmin'])?true:false;
    }

    /**
     * 是否VIP
     */
    public static function checkVIP(){
        return Yii::$app->getUser()->identity->vip==1?true:false;
    }

    /**
     * 检查用户数
     */
    public static function userMount(){
        $con = Yii::$app->db;
        $group = Yii::$app->getUser()->identity->group;
        $sql = 'select count(*) as a from '. User::tableName(). ' where `group`='. $group;
        $list = $con->createCommand($sql)->queryAll();
        return $list[0]['a'];
    }

}