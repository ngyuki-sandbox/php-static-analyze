# php-static-analyze

## init

**Phan**

https://github.com/phan/phan/wiki

```sh
bin/phan --init --init-overwrite --init-analyze-dir=src --init-level=1
bin/phan -p --color
```

**PHPStan**

https://phpstan.org/

```sh
phpstan analyse src
```

**Psalm**

https://psalm.dev/

```sh
bin/psalm --init src 1
bin/psalm
```

## ビルトインのジェネリック型

phpstan や psalm では SPL のコレクションなどはジェネリック型として定義されているけれども phan だとそうではない？
psalm だと下記にビルトインのジェネリック型がリストされているよなのだけど phan や phpstan に同じものはないかな？

> https://psalm.dev/docs/annotating_code/templated_annotations/#builtin-templated-classes-and-interfaces

phpstan については下記を参照すればいいでしょうか。

> https://github.com/phpstan/phpstan-src/tree/master/stubs

## 型アサーション

Phan や Psalm は型アサーションが可能です。

```php
/**
 * @param mixed $x
 * @phan-assert  array{aaa:string, bbb:int, ccc:bool} $x
 * @psalm-assert array{aaa:string, bbb:int, ccc:bool} $x
 */
function my_assert($x): void {
    if (!isset($x['aaa']) || !is_string($x['aaa'])) {
        throw new \InvalidArgumentException('oops!');
    }
    if (!isset($x['bbb']) || !is_int($x['bbb'])) {
        throw new \InvalidArgumentException('oops!');
    }
    if (!isset($x['ccc']) || !is_bool($x['ccc'])) {
        throw new \InvalidArgumentException('oops!');
    }
}

function main(): void {
    $x = json_decode('{"aaa": "str", "bbb": 123, "ccc": true}', true);
    my_assert($x);
    func($x);
}

/**
 * @phan-param  array{aaa:string, bbb:int, ccc:bool} $x
 * @psalm-param array{aaa:string, bbb:int, ccc:bool} $x
 * @phpstan-param array<mixed> $x
 */
function func(array $x): void {
    print_r($x);
}
```

この例では `my_assert` を例外にならずに抜けたとき、引数の `$x` は `array{aaa:string, bbb:int, ccc:bool}` 型であることが確認されます。

PHPStan には型アサーションはなさそうです。

## リテラルの Union のクラス定数やワイルドカード

PHPStan や Psalm ではリテラルの Union でクラス定数や定数名のワイルドカードが指定できます。

```php
class U {

    const STATUS_FOO = 'FOO';
    const STATUS_BAR = 'BAR';
    const XXX = 'XXX';

    /**
     * @phan-param   'FOO' | 'BAR' | 'XXX' $s
     * @phpstan-param self::STATUS_* | self::XXX $s
     * @psalm-param   self::STATUS_* | self::XXX $s
     */
    public static function status($s): void {
        print_r($s);
    }

    public function main(): void
    {
        self::status(self::STATUS_FOO);
        self::status(self::STATUS_BAR);
        self::status(self::XXX);
    }
}
```

PHPStan や Psalm では `self::STATUS_*` などとかけますが、Phan だと `'FOO' | 'BAR' | 'XXX'` のようにべた書きする必要があります。

## PhpStorm

PHPStan と Psalm は近いうちに PhpStorm でサポートされるかも。

- https://blog.jetbrains.com/phpstorm/2020/07/phpstan-and-psalm-support-coming-to-phpstorm/
