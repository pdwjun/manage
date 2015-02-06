<?php

namespace vova07\blogs\models\backend;

use vova07\rbac\models\Access;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * Blog search model.
 */
class BlogSearch extends Blog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Integer
            // String
            [['address', 'note'], 'string'],
            [['dbname', 'company'], 'string', 'max' => 255],
            // Status
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],
            // Date
            [['created_at', 'updated_at', 'starttime'], 'date', 'format' => 'd.m.Y']
        ];
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params Search params
     *
     * @return ActiveDataProvider DataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        if (!($this->load($params) && $this->validate())) {
//            return $dataProvider;
//        }

        $query->andFilterWhere(
            [
                'company' => $this->company,
                'status' => $this->status,
                'FROM_UNIXTIME(created_at, "%d.%m.%Y")' => $this->created_at!=""?$this->created_at:null,
                'FROM_UNIXTIME(updated_at, "%d.%m.%Y")' => $this->updated_at!=""?$this->updated_at:null,
            ]
        );
        //当前账号下 有关系的账套表 才显示
        $user_id = Yii::$app->getUser()->id;
        $list = Access::getCondomList($user_id);
        $lists = array();
        foreach ($list as $item) {
            $lists[] = $item['condom_id'];
        }
        if(!empty($lists))
            $query->andFilterWhere(['id'=> $lists]);
        else
            $query->andFilterWhere(['id'=> 0]); //没有账套



        $query->andFilterWhere(['like', 'company', $this->company]);
        $query->andFilterWhere(['like', 'dbname', $this->dbname]);
        $query->andFilterWhere(['like', 'address', $this->address]);
        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
