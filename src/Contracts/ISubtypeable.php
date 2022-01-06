<?php

namespace Moves\Eloquent\Subtypeable\Contracts;

interface ISubtypeable
{
    public function subtype(): ISubtypeable;
}
