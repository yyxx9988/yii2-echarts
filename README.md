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
use yyxx9988\yii2echarts\ECharts;

$echarts = new ECharts();

$result = $echarts->setTableName('test_tb')
    ->setFields([
        [
            'name' => 'f1',
            'titleText' => 'f1 titleText',
            'titleSub' => 'f1 titleSub',
            'seriesName' => 'f1 seriesName',
        ],
        [
            'name' => 'f2',
            'titleText' => 'f2 titleText',
            'titleSub' => 'f2 titleSub',
            'seriesName' => 'f2 seriesName',
        ],
    ])
    ->generatePie();

echo json_encode($result);
```

# License

MIT
