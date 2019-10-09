<?php

class _Context implements _Functor
{
    private $fn;
    private $value;

    public function __construct($value, callable $fn)
    {
        $this->value = $value;
        $this->fn = $fn;
    }

    public function unwrap()
    {
        return $this->value;
    }

    public function apply(callable $fn)
    {
        return $fn($this->unwrap());
    }

    public function fmap(callable $fn): _Functor
    {
        return new self($this->apply($fn), $this->fn);
    }
}

class _Maybe extends _Context implements _Functor
{
    public function __construct($value) 
    {
        parent::__construct($value, function ($v) {
            return is_null($v) ? new _Nothing : new _Just($v);
        });
    }
}


class _Nothing implements _Functor
{
    public function fmap(callable $fn): _Functor
    {
        return $this;
    }

    public function unwrap()
    {
        return $this;
    }

    public function apply(callable $fn)
    {
        return $this;
    }
}

class _Just extends _Context
{
    public function __construct($value)
    {
        parent::__construct($value, function ($v) {
            return $v;
        });
    }
}

interface _Functor
{
    public function fmap(callable $fn): _Functor;
    public function unwrap();
    public function apply(callable $fn);
}

// https://gist.github.com/jimbocoder/fe720b4f70376d6bcef0
function _compose() {
    $fxns = func_get_args();
    $outer = function($identity) { return $identity; };
    while($f = array_pop($fxns)) {
        if ( !is_callable($f) ) {
            throw new \Exception('This should be a better exception.');
        }
        $outer = function() use($f, $outer) {
            return $f(call_user_func_array($outer, func_get_args()));
        };
    }
    return $outer;
}

class _Function implements _Functor
{
    private $fn;

    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    public function __invoke()
    {
        return $this->fn();
    }

    public function fmap(callable $fn): _Functor
    {
        return new self(_compose($fn, $this->fn));
    }
    public function unwrap()
    {
        // ?
    }
    public function apply(callable $fn)
    {
        // ?
    }
}

class _Array implements ArrayAccess, _Functor {
    private $container = [];

    public function __construct(array $array = []) {
        $this->container = $array;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return $this->container[$offset] ?? null;;
    }

    public function fmap(callable $fn): _Functor
    {
        return new self(array_map($fn, $this->container));
    }

    public function unwrap()
    {
        return $this->container;
    }

    public function apply(callable $fn)
    {
        return array_map($fn, $this->unwrap());
    }
}

$arr = new _Array([1, 2, 3]);
function add1($x)
{
    return $x + 1;
}

function sub2($x)
{
    return $x - 2;
}

$result = $arr
    ->fmap('add1')
    ->fmap('sub2')
    ->fmap('sub2')
;
var_dump($result);

$a = new _Maybe(2);
$result = $a
    ->fmap('add1')
;
echo "**************\n";
var_dump($result);

$a = new _Just(20);
$result = $a
    ->fmap('add1')
;
echo "**************\n";
var_dump($result);


$a = new _Nothing;
$result = $a
    ->fmap('add1')
;
echo "**************\n";
var_dump($result);


$a = new _Function('add1');
$b = new _Just(20);
$result = $a
    ->apply($b)
;
echo "**************\n";
var_dump($result);