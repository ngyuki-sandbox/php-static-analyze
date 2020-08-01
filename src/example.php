<?php
declare(strict_types=1);

namespace ArrayObjectTemplate {

    use Symfony\Component\Console\Command\Command;

    /**
     * @param \ArrayObject<mixed,Command> $cc
     *
     * @phan-param    \ArrayObject & iterable<Command> $cc
     * @phpstan-param \ArrayObject<mixed,Command> $cc
     * @psalm-param   \ArrayObject<mixed,Command> $cc
     */
    function x($cc): void
    {
        $cc->getArrayCopy();
        foreach ($cc as $c) {
            $c->getHelperSet();
        }
    }
}

namespace ArrayKeyValue {

    /**
     * @param array<int,string> $aa
     */
    function f($aa): void
    {
        foreach ($aa as $k => $v) {
            v($k, $v);
        }
    }

    function x(): void
    {
        $a = [123 => 'abc'];
        f($a);
    }

    function v(int $n, string $s): void
    {
        print_r([$n, $s]);
    }
}

namespace ArrayAssoc {

    /**
     * @param array{aaa:string, bbb:int, ccc?:bool} $params
     */
    function f($params): void
    {
        print_r($params);
    }

    function x(): void
    {
        $a = ['aaa' => 'str', 'bbb' => 123, 'ccc' => true];
        f($a);

        $b = ['aaa' => 'str', 'bbb' => 123];
        f($b);
    }
}

namespace LiteralUnion {

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
}

namespace Magic {

    /**
     * @phan-forbid-undeclared-magic-properties
     * @phan-forbid-undeclared-magic-methods
     * @psalm-seal-properties
     *
     * @property int $prop
     * @method int method()
     */
    class M {

        public function __get(string $name): string { return $name; }

        /**
         * @param string $name
         * @param mixed  $value
         */
        public function __set(string $name, $value): void { print_r([$name, $value]); }

        /**
         * @param string  $name
         * @param mixed[] $args
         */
        public function __call(string $name, array $args): void { print_r([$name, $args]); }

        public function x(): void
        {
            $prop = $this->prop;
            $this->prop = $prop;

            $this->method();
        }
    }
}

namespace Internal\A {
    /**
     * @internal
     * @psalm-internal Internal\A
     */
    class AI {}
    class AA  extends AI {}

}
namespace Internal\B {
    class BB extends \Internal\A\AA {}
    //class BI extends \Internal\A\AI {}
}

namespace Override {

    class BaseClass {
        public function f(): bool { return false; }
    }

    class SubClass extends BaseClass {
        /** @phan-override */
        public function f(): bool { return true; }

        /** @phan-override */
        //public function x(): bool { return true; }
    }
}

namespace Assertion {

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
}
