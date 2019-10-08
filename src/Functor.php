<?php
declare(strict_types=1);

namespace Ferge\Functional;

interface Functor
{
    public function map(callable $lambda): Functor;
    public function apply(callable $lambda): Functor;
}
