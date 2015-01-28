<?php

namespace vova07\blogs\models\backend;

use yii\data\ActiveDataProvider;

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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'company' => $this->company,
                'status' => $this->status,
                'FROM_UNIXTIME(created_at, "%d.%m.%Y")' => $this->created_at!=""?$this->created_at:null,
                'FROM_UNIXTIME(updated_at, "%d.%m.%Y")' => $this->updated_at!=""?$this->updated_at:null,
            ]
        );

        $query->andFilterWhere(['like', 'company', $this->company]);
        $query->andFilterWhere(['like', 'dbname', $this->dbname]);
        $query->andFilterWhere(['like', 'address', $this->address]);
        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
