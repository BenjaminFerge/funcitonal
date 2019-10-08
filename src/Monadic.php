<?php
declare(strict_types=1);

namespace Ferge\Functional;

interface Monadic extends Functor
{
    public function return();
    public function join(callable $lambda);
    public function fmap(callable $lambda): callable;
}
