# rector-tutorial

## 1. [Create Your Own Rector](https://github.com/rectorphp/rector/blob/main/docs/create_own_rule.md)

```
composer install
./vendor/bin/rector process src --dry-run
```

## 2. [Grerate from recipe](https://github.com/rectorphp/rector-generator)



```
git chcheckout master
./vendor/bin/phpunit --no-configuration /Users/akihito/git/rector-bearsunday/rules-tests 
```

Edit `RenameMethodCallRector::rector()` method

```php
$node->name->name = 'somethingElse';
// change the node
return $node;
```

done!
