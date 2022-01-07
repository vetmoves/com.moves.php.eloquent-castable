<?php

namespace Tests\TestCases;

use Moves\Eloquent\Subtypeable\Exceptions\SubtypeException;
use Tests\Models\ChildClassA;
use Tests\Models\ChildClassB;
use Tests\Models\ChildClassC;
use Tests\Models\ParentClass;

class TSubtypeableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ParentClass::truncate();
    }

    public function testManualSubtype()
    {
        $instance = new ParentClass(['subtype_class' => ChildClassA::class, 'property' => 123]);
        $child = $instance->subtype();
        $this->assertInstanceOf(ChildClassA::class, $child);
        $this->assertEquals($instance->property, $child->property);

        $instance = new ParentClass(['subtype_class' => ChildClassB::class, 'property' => 123]);
        $child = $instance->subtype();
        $this->assertInstanceOf(ChildClassB::class, $child);
        $this->assertEquals($instance->property, $child->property);

        $instance = new ParentClass(['subtype_class' => ChildClassC::class, 'property' => 123]);
        $child = $instance->subtype();
        $this->assertInstanceOf(ChildClassC::class, $child);
        $this->assertEquals($instance->property, $child->property);

        $instance = new ParentClass(['subtype_class' => 'GarbageClass', 'property' => 123]);
        $child = $instance->subtype();
        $this->assertInstanceOf(ParentClass::class, $child);
        $this->assertEquals($instance->property, $child->property);
    }

    public function testManualSubtypeWithEmptySubtypeKey()
    {
        $instance = new ParentClass(['subtype_class' => '', 'property' => 123]);

        $this->expectException(SubtypeException::class);
        $this->expectExceptionMessage('empty');

        $instance->subtype(true);
    }

    public function testManualSubtypeWithSubtypeKeyInvalidClass()
    {
        $instance = new ParentClass(['subtype_class' => 'ClassABC', 'property' => 123]);

        $this->expectException(SubtypeException::class);
        $this->expectExceptionMessage('not find');

        $instance->subtype(true);
    }

    public function testManualSubtypeWithSubtypeKeyNotValidSubclass()
    {
        $instance = new ParentClass(['subtype_class' => self::class, 'property' => 123]);

        $this->expectException(SubtypeException::class);
        $this->expectExceptionMessage('subclass');

        $instance->subtype(true);
    }

    public function testManualSubtypeWithCorrectType()
    {
        $instance = new ChildClassA(['subtype_class' => ChildClassA::class, 'property' => 123]);

        $this->expectException(SubtypeException::class);
        $this->expectExceptionMessage('correct');

        $instance->subtype(true);
    }

    public function testAutomaticSubtype()
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

    public function testSubtypeOverridesMethod()
    {
        $instance = new ParentClass(['subtype_class' => ChildClassA::class, 'property' => 123]);
        $this->assertTrue($instance->getSubtypeOverridesMethod('testMethod'));

        $instance = new ParentClass(['subtype_class' => ChildClassB::class, 'property' => 123]);
        $this->assertFalse($instance->getSubtypeOverridesMethod('testMethod'));
    }

    public function testChildCastsMergesIntoParentCasts()
    {
        $instance = new ParentClass(['subtype_class' => ChildClassA::class, 'property' => 123]);
        $child = $instance->subtype();
        $this->assertNotEquals($instance->casts, $child->casts);

        $instance = new ParentClass(['subtype_class' => ChildClassB::class, 'property' => 123]);
        $child = $instance->subtype();
        $this->assertEquals($instance->casts, $child->casts);
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
        $this->assertEquals(ChildClassA::class, $aChildren[0]->subtype_class);

        $bChildren = ChildClassB::all();

        $this->assertCount(1, $bChildren);
        $this->assertInstanceOf(ChildClassB::class, $bChildren[0]);
        $this->assertEquals(ChildClassB::class, $bChildren[0]->subtype_class);

        $cChildren = ChildClassC::all();

        $this->assertCount(1, $cChildren);
        $this->assertInstanceOf(ChildClassC::class, $cChildren[0]);
        $this->assertEquals(ChildClassC::class, $cChildren[0]->subtype_class);
    }
}
