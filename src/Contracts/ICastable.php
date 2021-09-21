<?php

namespace Moves\Eloquent\Castable\Contracts;

interface ICastable
{
    public function cast(): ICastable;
}