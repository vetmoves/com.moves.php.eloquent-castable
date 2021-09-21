<?php

namespace Tests\TestCases;

use PHPUnit\Framework\TestCase;
use Tests\Models\ChildClassA;
use Tests\Models\ChildClassB;
use Tests\Models\ChildClassC;
use Tests\Models\ParentClass;

class CastableTest extends TestCase
{
    public function testManualCast() {
        $instance = new ParentClass(['type' => ChildClassA::class]);
        $child = $instance->cast();
        $this->assertInstanceOf(ChildClassA::class, $child);

        $instance = new ParentClass(['type' => ChildClassB::class]);
        $child = $instance->cast();
        $this->assertInstanceOf(ChildClassB::class, $child);

        $instance = new ParentClass(['type' => ChildClassC::class]);
        $child = $instance->cast();
        $this->assertInstanceOf(ChildClassC::class, $child);

        $instance = new ParentClass(['type' => 'GarbageClass']);
        $child = $instance->cast();
        $this->assertInstanceOf(ParentClass::class, $child);
    }

//    public function testAutomaticCast() {
//        $all = ParentClass::all();
//
//        // Instances contained in $all are not of type ParentClass,
//        // but of type ChildClassA, ChildClassB, and ChildClassC
//    }
}