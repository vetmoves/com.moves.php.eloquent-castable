<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Moves\Eloquent\Subtypeable\Contracts\ISubtyepable;
use Moves\Eloquent\Subtypeable\Traits\TSubtypeable;

/**
 * Class ParentClass
 * @package Tests\Models
 *
 * @property string $type
 * @property int $property
 */
class ParentClass extends Model implements ISubtyepable
{
    use TSubtypeable;

    public $fillable = ['subtype_class', 'property', 'abc123'];

    public $casts = ['abc123' => 'array'];

    public function getSubtypeOverridesMethod(string $method): bool
    {
        return $this->subtypeOverridesMethod($method);
    }

    protected function testMethod(): bool
    {
        return true;
    }
}
