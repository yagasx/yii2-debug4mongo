<p align="center">
    <a href="https://github.com/yagasx/yii2-debug4mongo" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Debug For MongoDB</h1>
    <br>
</p>

本项目为yii2-debug的扩展，使用MongoDB对debug数据进行存储。

目录结构
-------------------

      src/                 代码目录
      src/models/          数据模型
      src/views/           视图文件
      src/controllers/     控制器


安装依赖
------------

- PHP支持>=5.4
- yii2-mongodb
- yii2-debug支持>=2.1.25(基于此版本构建而来)


安装说明
------------
~~~
composer require yagas/yii2-debug4mongo
~~~

配置说明
------------
```php
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yagas\debug\Module',
        'logTarget' => [
            'class' => 'yagas\debug\LogTarget',
            'app_no' => 'localhost_001', // 为当前站点设定标识
        ],
        'percent' => 10, // 百分之十的几率清除历史数据(GC)
    ];
}
```