<?php

namespace Moves\Eloquent\Subtypeable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Moves\Eloquent\Subtypeable\Contracts\ISubtyepable;

trait TSubtypeable
{
    public static $SUBTYPE_KEY = 'subtype_class';

    public static function bootTSubtypeable() {
        static::creating(function (Model $model) {
            if (is_null($model->getAttribute(static::$SUBTYPE_KEY))) {
                $model->setAttribute(static::$SUBTYPE_KEY, static::class);
            }
        });

        if (is_subclass_of(static::class, self::class)) {
            static::addGlobalScope(static::$SUBTYPE_KEY, function (Builder $builder) {
                $builder->where(static::$SUBTYPE_KEY, static::class);
            });
        }
    }

    public function subtype(): ISubtyepable {
        $subtype = $this->getAttribute(static::$SUBTYPE_KEY);

        $currentClass = get_class($this);

        if (class_exists($subtype) && $currentClass != $subtype) {
            /** @var Model|ISubtyepable $model */
            $model = new $subtype();
            $model->setRawAttributes($this->attributes);
            $model->setConnection($this->connection);
            $model->exists = $this->exists;

            return $model;
        }

        return $this;
    }

    protected function subtypeOverridesMethod(string $method): bool
    {
        $reflector = new \ReflectionMethod($this->subtype(), $method);

        return $reflector->getDeclaringClass()->getName() != self::class;
    }

    /**
     * Override default Model behavior
     *
     * @see \Illuminate\Database\Eloquent\Model
     * @return string
     */
    public function getTable()
    {
        return $this->table ?? Str::snake(Str::pluralStudly(class_basename(self::class)));
    }

    /**
     * Override default Model behavior
     *
     * @see \Illuminate\Database\Eloquent\Model
     * @param array $attributes
     * @param null $connection
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance(array_intersect_key((array) $attributes, array_flip([static::$SUBTYPE_KEY])), true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * Override default Model behavior
     *
     * @see \Illuminate\Database\Eloquent\Model
     * @param array $attributes
     * @param false $exists
     * @return $this
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $className = static::class;

        if (array_key_exists(static::$SUBTYPE_KEY, $attributes))
        {
            if (
                class_exists($attributes[static::$SUBTYPE_KEY]) &&
                is_subclass_of($attributes[static::$SUBTYPE_KEY], static::class)
            )
            {
                $className = $attributes[static::$SUBTYPE_KEY];
            }

            if (count($attributes) == 1)
            {
                $attributes = [];
            }
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

    /**
     * Override default Model behavior
     *
     * @see \Illuminate\Database\Eloquent\Concerns\HasAttributes
     * @param  array  $casts
     * @return $this
     */
    public function mergeCasts($casts)
    {
        $this->casts = array_merge($casts, $this->casts);

        return $this;
    }
}
