<?php

namespace Tests\TestCases;

use Tests\Models\ChildClassA;
use Tests\Models\ChildClassB;
use Tests\Models\ChildClassC;
use Tests\Models\ParentClass;

class TCastableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ParentClass::truncate();
    }

    public function testManualCast()
    {
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

    public function testAutomaticCast()
    {
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

    public function testChildAssumesParentTable()
    {
        $parent = new ParentClass([]);
        $child = new ChildClassA([]);

        $this->assertEquals($parent->getTable(), $child->getTable());
    }

    public function testChildAppliesGlobalScope()
    {
        ChildClassA::create([]);
        ChildClassB::create([]);
        ChildClassC::create([]);

        $aChildren = ChildClassA::all();

        $this->assertCount(1, $aChildren);
        $this->assertInstanceOf(ChildClassA::class, $aChildren[0]);
        $this->assertEquals(ChildClassA::class, $aChildren[0]->cast_type);

        $bChildren = ChildClassB::all();

        $this->assertCount(1, $bChildren);
        $this->assertInstanceOf(ChildClassB::class, $bChildren[0]);
        $this->assertEquals(ChildClassB::class, $bChildren[0]->cast_type);

        $cChildren = ChildClassC::all();

        $this->assertCount(1, $cChildren);
        $this->assertInstanceOf(ChildClassC::class, $cChildren[0]);
        $this->assertEquals(ChildClassC::class, $cChildren[0]->cast_type);
    }
}
