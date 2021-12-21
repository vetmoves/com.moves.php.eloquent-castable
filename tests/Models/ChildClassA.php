<?php


namespace Tests\Models;


class ChildClassA extends ParentClass
{
    public $casts = ['abc123' => 'object'];

    protected function testMethod(): bool
    {
        return false;
    }
}
