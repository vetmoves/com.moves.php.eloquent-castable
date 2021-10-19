<?php

namespace Tests\TestCases;

use Tests\Models\ChildClassA;
use Tests\Models\ChildClassB;
use Tests\Models\ChildClassC;
use Tests\Models\ParentClass;

class TCastableTest extends TestCase
{
    public function testManualCast() {
        $instance = new ParentClass(['cast_type' => ChildClassA::class]);
        $child = $instance->cast();
        $this->assertInstanceOf(ChildClassA::class, $child);

        $instance = new ParentClass(['cast_type' => ChildClassB::class]);
        $child = $instance->cast();
        $this->assertInstanceOf(ChildClassB::class, $child);

        $instance = new ParentClass(['cast_type' => ChildClassC::class]);
        $child = $instance->cast();
        $this->assertInstanceOf(ChildClassC::class, $child);

        $instance = new ParentClass(['cast_type' => 'GarbageClass']);
        $child = $instance->cast();
        $this->assertInstanceOf(ParentClass::class, $child);
    }

    public function testAutomaticCast() {
        ParentClass::truncate();

        ChildClassA::create([]);
        ChildClassB::create([]);
        ChildClassC::create([]);

        $all = ParentClass::all();

        // Instances contained in $all are not of type ParentClass,
        // but of type ChildClassA, ChildClassB, and ChildClassC
        $this->assertCount(3, $all);
        $this->assertInstanceOf(ChildClassA::class, $all[0]);
        $this->assertInstanceOf(ChildClassB::class, $all[1]);
        $this->assertInstanceOf(ChildClassC::class, $all[2]);
    }
}
