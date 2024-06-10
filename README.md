# Rector Rules for BEAR.Sunday (v2)

The [rector/rector](http://github.com/rectorphp/rector) rules for BEAR.Sunday.

## Rules

### RayQueryModule

Change #[Named] to #[Sql] in Ray.QueryModule.

```diff
class SomeClass
{
-    #[Named('add_todo_item')] callable $todo
+    #[\Ray\Query\Annotation\Sql('todo_item_by_id')] \Ray\Query\InvokeInterface $todo
}
```

## Install

```bash
composer require bearsunday/rector-bearsunday 2.x-dev --dev
```

## Install with Composer Bin Plugin

```php
composer require --dev bamarni/composer-bin-plugin
composer bin rector require bearsunday/rector-bearsunday 2.x-dev --dev
cp ./vendor-bin/rector/vendor/bearsunday/rector-bearsunday/rector.php rector-bearsunday.php
```
