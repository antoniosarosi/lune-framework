<?php

namespace Lune\Database;

/**
 * Database model.
 */
abstract class Model {
    /**
     * Database table.
     */
    protected ?string $table = null;

    /**
     * Id and primary key column.
     */
    protected string $primaryKey = "id";

    /**
     * Hidden properties.
     */
    protected array $hidden = [];

    /**
     * Mass assignable attributes.
     */
    protected array $fillable = [];

    /**
     * Columns
     */
    protected array $columns = [];

    /**
     * Automatically insert `created_at` and `updated_at` columns.
     */
    protected $insertTimestamps = true;

    /**
     * Initialize model.
     */
    public function __construct() {
        if ($this->table == null) {
            $subclass = new \ReflectionClass(static::class);
            $this->table = snake_case("{$subclass->getShortName()}s");
        }
    }

    /**
     * Mark any property that is being set on this object as a column.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->columns[$name] = $value;
    }

    /**
     * Get previously set property.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        return $this->columns[$name] ?? null;
    }

    /**
     * Hide properties that shouldn't be serialized.
     *
     * @return array
     */
    public function __sleep(): array {
        foreach ($this->hidden as $hide) {
            unset($this->columns[$hide]);
        }

        return array_keys(get_object_vars($this));
    }

    /**
     * Set model properties from database columns.
     *
     * @param array<string, mixed> $columns
     * @return $this
     */
    protected function setProperties(array $columns): static {
        foreach ($columns as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * Assign all assignables attributes at once.
     *
     * @param array $attributes
     * @return static
     */
    protected function massAsign(array $attributes): static {
        if (count($this->fillable) == 0) {
            throw new \BadMethodCallException("Model " . static::class . " does not have fillable attributes");
        }

        return $this->setProperties($attributes);
    }

    /**
     * Save the current model in the database.
     */
    public function save(): static {
        $modelColumns = $this->columns;
        if ($this->insertTimestamps) {
            $modelColumns["created_at"] = date("Y-m-d H:m:s");
        }
        $dbColumns = implode(",", array_keys($modelColumns));
        $bind = implode(",", array_fill(0, count($modelColumns), "?"));
        DB::statement("INSERT INTO $this->table ($dbColumns) VALUES ($bind)", array_values($modelColumns));

        return $this;
    }

    /**
     * Find model with given `$id`.
     *
     * @param int $id
     * @return ?static
     */
    public static function find(int $id): ?static {
        $model = new static();
        $rows = DB::statement("SELECT * FROM $model->table WHERE $model->primaryKey = ?", [$id]);
        if (count($rows) == 0) {
            return null;
        }

        return $model->setProperties($rows[0]);
    }

    /**
     * First inserted model.
     *
     * @return ?static
     */
    public static function first(): ?static {
        $model = new static();
        $rows = DB::statement("SELECT * FROM $model->table ORDER BY $model->primaryKey");
        if (count($rows) == 0) {
            return null;
        }

        return $model->setProperties($rows[0]);
    }

    /**
     * Get all the models in the database.
     * @return static[]
     */
    public static function all(): array {
        $model = new static();
        $rows = DB::statement("SELECT * FROM $model->table");
        if (count($rows) == 0) {
            return [];
        }

        $model->setProperties($rows[0]);

        $models = [$model];

        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setProperties($rows[$i]);
        }

        return $models;
    }

    /**
     * Store model in the database.
     * @return self
     */
    public static function create(array $columns) {
        return (new static())->massAsign($columns)->save();
    }

    /**
     * Get the models where `$column = $value`
     * @param string $column
     * @param mixed $value
     * @return static[]
     */
    public static function where(string $column, mixed $value): array {
        $model = new static();
        $rows = DB::statement("SELECT * FROM $model->table WHERE $column = ?", [$value]);
        if (count($rows) == 0) {
            return [];
        }

        $model->setProperties($rows[0]);

        $models = [$model];

        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setProperties($rows[$i]);
        }

        return $models;
    }

    /**
     * Get the first model where `$column = $value`
     * @param string $column
     * @param mixed $value
     * @return ?static
     */
    public static function firstWhere(string $column, mixed $value): ?static {
        $model = new static();
        $rows = DB::statement("SELECT * FROM $model->table WHERE $column = ? ORDER BY $model->primaryKey LIMIT 1", [$value]);
        if (count($rows) == 0) {
            return null;
        }

        return $model->setProperties($rows[0]);
    }
}
