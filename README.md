# Laravel Snowflake

## 环境需求
- PHP: `^8.2`
- Laravel: `^11`

## 安装
```shell
composer require mitoop/laravel-snowflake
```

## 发布配置文件
```shell
php artisan vendor:publish --provider="Mitoop\LaravelSnowflake\ServiceProvider"
```

`snowflake.php`配置文件
```php
return [
    // 纪元时间
    'epoch' => '2023-08-01 00:00:00',
    // 数据中心id 范围:[0-31]. 为 null时, 随机取[0-31]的值
    'datacenter_id' => null,
    // 工作机器id 范围:[0-31]. 为 null时, 随机取[0-31]的值
    'worker_id' => null,
    // 序列号生成策略类 为 null 时, 使用随机数. 自定义策略请返回闭包.
    'sequence_strategy' => null,
];
```

## 可用方法
1. 模型类可使用 `HasSnowflakeIds` trait, 支持多字段, 具体用法同 `HasUuids`, `HasUlids` [Doc](https://laravel.com/docs/10.x/eloquent#uuid-and-ulid-keys)
2. Str 快捷方法 `\Str::snowflakeId()`
3. Blueprint方法
```php
    Schema::create('tests', function (Blueprint $table){
         $table->snowflake()->primary();
         $table->snowflake('another_snowflake_id');
         $table->timestamps();
    });
```
