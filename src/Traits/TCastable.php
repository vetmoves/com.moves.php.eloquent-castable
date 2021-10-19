<?php

namespace Moves\Eloquent\Castable\Traits;

use Illuminate\Database\Eloquent\Model;
use Moves\Eloquent\Castable\Contracts\ICastable;

trait TCastable
{
    public $castTypeKey = 'cast_type';
    
    public static function bootTCastable() {
        static::creating(function (Model $model) {
            if (is_null($model->getAttribute($model->castTypeKey))) {
                $model->setAttribute($model->castTypeKey, static::class);
            }
        });
    }

    public function cast(): ICastable {
        $castType = $this->getAttribute($this->castTypeKey);

        if (class_exists($castType)) {
            return new $castType;
        } else {
            return $this;
        }
    }

    /**
     * Override default Model behavior
     *
     * @see https://github.com/illuminate/database/blob/master/Eloquent/Model.php
     * @param array $attributes
     * @param null $connection
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance(array_intersect_key((array) $attributes, array_flip([$this->castTypeKey])), true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * Override default Model behavior
     *
     * @see https://github.com/illuminate/database/blob/master/Eloquent/Model.php
     * @param array $attributes
     * @param false $exists
     * @return $this
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $className = static::class;

        if (
            array_key_exists($this->castTypeKey, $attributes) &&
            class_exists($attributes[$this->castTypeKey]) &&
            is_subclass_of($attributes[$this->castTypeKey], static::class)
        ) {
            $className = $attributes[$this->castTypeKey];
        }

        $model = new $className((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable($this->getTable());

        $model->mergeCasts($this->casts);

        return $model;
    }
}
