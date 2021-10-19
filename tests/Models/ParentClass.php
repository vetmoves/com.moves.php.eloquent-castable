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
 */
class ParentClass extends Model implements ICastable
{
    use TCastable;

    public $fillable = ['cast_type'];
}
