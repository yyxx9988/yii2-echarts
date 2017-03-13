<h1 align="center">Yii2 ECharts</h1>

<p align="center">A yii2 extension that can auto export echarts config by analysis database table schema.</p>

# Requirement

- PHP >= 5.4

# Installation

```shell
$ composer require "yyxx9988/yii2echarts"
```

# Usage

```php
use \yyxx9988\yii2echarts\ECharts;

$echarts = new ECharts();

$result = $echarts->setTableName('test_tb')
    ->setFields(['f1', 'f2'])
    ->setTitleText('test title')
    ->setTitleSub('test sub title')
    ->setSeriesName('test series name')
    ->generatePie();

echo json_encode($result);
```

# License

MIT
