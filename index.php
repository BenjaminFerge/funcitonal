<?php
declare(strict_types=1);

use Ferge\Functional\Functor;

require __DIR__.'/vendor/autoload.php';

class MyFunctor implements Functor
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function map(callable $lambda): Functor
    {
        return new self(array_map($lambda, $this->value));
    }

    public function apply(callable $lambda): Functor
    {
        return new self($lambda($this->value));
    }
}

$functor = new MyFunctor(1);
$result = $functor
    ->apply(function($x) { return $x+=1;})
    ->apply(function($x) { return $x*=2;})
;
var_dump($result);

$functor = new MyFunctor([1, 2, 3]);
$result = $functor
    ->map(function($x) { return $x*=2;})
;
var_dump($result);
