<?php

namespace Moves\Eloquent\Subtypeable\Contracts;

interface ISubtyepable
{
    public function subtype(): ISubtyepable;
}
