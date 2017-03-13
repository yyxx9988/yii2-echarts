<?php

namespace yyxx9988\yii2echarts;

use Yii;
use yii\db\Query;

class ECharts
{
    public $tableName;
    public $fields = [];
    public $error;

    public $titleText;
    public $titleSub;
    public $seriesName;

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function setFields($fields)
    {
        if (is_array($fields)) {
            $this->fields = $fields;
        }

        return $this;
    }

    public function setTitleText($titleText)
    {
        $this->titleText = $titleText;

        return $this;
    }

    public function setTitleSub($titleSub)
    {
        $this->titleSub = $titleSub;

        return $this;
    }

    public function setSeriesName($seriesName)
    {
        $this->seriesName = $seriesName;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function generatePie()
    {
        $result = [];

        $Schema = Yii::$app->getDb()->getTableSchema($this->tableName);
        if (!$Schema) {
            $this->error = "table $this->tableName does not exists";
            return false;
        }
        $columnNames = $Schema->getColumnNames();
        if (count($this->fields) > 0) {
            $columnNames = array_intersect($columnNames, $this->fields);
        }
        foreach ($columnNames as $v) {
            $col = $Schema->getColumn($v);
            // verify dbType
            if (strpos($col->dbType, 'tinyint(1)') === false) {
                $this->error = "column $v's dbType should be tinyint(1)";
                return false;
            }
            // verify comment
            if (!preg_match('#^(.*[^ ])\s+(.*)#u', trim($col->comment), $matches)) {
                $this->error = "unable to verify comment ($col->comment)";
                return false;
            }
            $cvs = array_merge(explode('|', $matches[2]));
            foreach ($cvs as $kk => $vv) {
                $result[] = [
                    'title' => [
                        'text' => $this->titleText ? $this->titleText : 'Undefined titleText',
                        'subtext' => $this->titleSub ? $this->titleSub : '',
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
                        'name' => $this->seriesName ? $this->seriesName : 'Undefined seriesName',
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
