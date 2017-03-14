<?php

namespace yyxx9988\yii2echarts;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ECharts
{
    /**
     * @var string
     */
    public $tableName;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var string
     */
    private $_error;

    /**
     * @param string $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields($fields)
    {
        if (ArrayHelper::isAssociative($fields)) {
            $this->fields = $fields;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return array|bool
     */
    public function generatePie()
    {
        $result = [];

        $Schema = Yii::$app->getDb()->getTableSchema($this->tableName);
        if (!$Schema) {
            $this->_error = "table $this->tableName does not exists";
            return false;
        }

        $columnNames = $Schema->getColumnNames();
        if (count($this->fields) > 0) {
            $columnNames = array_intersect($columnNames, array_column($this->fields, 'name'));
        }
        foreach ($columnNames as $v) {
            $col = $Schema->getColumn($v);
            // verify dbType
            if (strpos($col->dbType, 'tinyint(1)') === false) {
                $this->_error = "column $v's dbType should be tinyint(1)";
                return false;
            }
            // verify comment
            if (!preg_match('#^(.*[^ ])\s+(.*)#u', trim($col->comment), $matches)) {
                $this->_error = "unable to verify comment ($col->comment)";
                return false;
            }

            $key = array_search($v, array_column($this->fields, 'name'));

            $result[$v] = [
                'title' => [
                    'text' => $this->fields[$key]['titleText'] ? $this->fields[$key]['titleText'] : 'Undefined titleText',
                    'subtext' => $this->fields[$key]['titleSub'] ? $this->fields[$key]['titleSub'] : '',
                    'x' => 'center',
                ],
                'tooltip' => [
                    'trigger' => 'item',
                    'formatter' => '{a} <br/>{b} : {c} ({d}%)',
                ],
                'legend' => [
                    'orient' => 'vertical',
                    'left' => 'left',
                    'data' => [],
                ],
                'series' => [
                    'name' => $this->fields[$key]['seriesName'] ? $this->fields[$key]['seriesName'] : 'Undefined seriesName',
                    'type' => 'pie',
                    'radius' => '55%',
                    'center' => ['50%', '60%'],
                    'data' => [],
                    'itemStyle' => [
                        'emphasis' => [
                            'shadowBlur' => 10,
                            'shadowOffsetX' => 0,
                            'shadowColor' => 'rgba(0, 0, 0, 0.5)',
                        ]
                    ]
                ]
            ];

            $cvs = array_merge(explode('|', $matches[2]));
            foreach ($cvs as $kk => $vv) {
                $result[$v]['legend']['data'][$kk] = $vv;
                $result[$v]['series']['data'][$kk]['name'] = $vv;
                $result[$v]['series']['data'][$kk]['value'] =
                    (new Query())->from($this->tableName)
                        ->where([
                            $v => $vv
                        ])
                        ->count();
            }
        }

        return $result;
    }
}
