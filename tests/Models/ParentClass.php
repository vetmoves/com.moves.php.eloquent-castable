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

    public $fillable = ['type'];

    public function cast(): ICastable {
        if (class_exists($this->type)) {
            return new $this->type;
        } else {
            return $this;
        }
    }
}