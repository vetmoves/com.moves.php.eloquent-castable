<?php

namespace Moves\Eloquent\Castable\Traits;

trait TCastable
{
    /**
     * Override default Model behavior
     *
     * @see https://github.com/illuminate/database/blob/master/Eloquent/Model.php#L510-L521
     * @param array $attributes
     * @param false $exists
     * @return $this
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $className = static::class;

        if (
            array_key_exists('casts', $attributes) &&
            class_exists($attributes['casts']) &&
            is_subclass_of($attributes['casts'], static::class)
        ) {
            $className = $attributes['casts'];
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