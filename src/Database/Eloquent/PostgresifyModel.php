<?php

namespace Aejnsn\Postgresify\Database\Eloquent;

use Aejnsn\Postgresify\PostgresifyTypeCaster;
use Aejnsn\Postgresify\Types\IntegerRange;
use Aejnsn\Postgresify\Types\NumericRange;
use Illuminate\Database\Eloquent\Model;
use Smiarowski\Postgres\Model\Traits\PostgresArray;

class PostgresifyModel extends Model
{
    use PostgresArray;

    public function setAttribute($key, $value)
    {
        if ($this->hasCast($key)) {
            switch ($this->getCastType($key)) {
                case 'array':
                    $value = self::mutateToPgArray($value);
                    break;
            }
        }
        return parent::setAttribute($key, $value);
    }

    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'array':
                return self::accessPgArray($value);
            case 'numericrange':
                return (new NumericRange(0, 0))->fromPgValues($value);
            case 'integerrange':
                return (new IntegerRange(0, 0))->fromPgValues($value);
        }

        return parent::castAttribute($key, $value);
    }

    /**
     * Override to exclude array as JSON castable.
     *
     * @param  string $key
     * @return bool
     */
    protected function isJsonCastable($key)
    {
        return $this->hasCast($key, ['json', 'object', 'collection']);
    }
}

