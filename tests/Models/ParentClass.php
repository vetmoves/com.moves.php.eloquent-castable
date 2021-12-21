<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Moves\Eloquent\Castable\Contracts\ICastable;
use Moves\Eloquent\Castable\Traits\TCastable;

/**
 * Class ParentClass
 * @package Tests\Models
 *
 * @property string $type
 * @property int $property
 */
class ParentClass extends Model implements ICastable
{
    use TCastable;

    public $fillable = ['cast_type', 'property', 'abc123'];

    public $casts = ['abc123' => 'array'];

    public function getCastOverridesMethod(string $method): bool
    {
        return $this->castOverridesMethod($method);
    }

    protected function testMethod(): bool
    {
        return true;
    }
}
