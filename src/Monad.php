<?php
declare(strict_types=1);

namespace Ferge\Functional;

use Ferge\Functional;

abstract class Monad implements Monadic
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;    
    }

    abstract public function return();
    abstract public function join(callable $lambda);
    abstract public function fmap(callable $lambda): callable;
    
    public function bind(callable $lambda)
    {
        return $this->join($this->fmap($lambda));
    }

    public function chain(callable $lambda)
    {
        return $this->join($this->fmap($lambda));
    }

    public function extract()
    {
        return $this->value;
    }
}
