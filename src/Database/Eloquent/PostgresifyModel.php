<?php

namespace Aejnsn\Postgresify\Database\Eloquent;

use Aejnsn\Postgresify\PostgresifyTypeCaster;
use Illuminate\Database\Eloquent\Model;

class PostgresifyModel extends Model
{
    use PostgresArray;

    protected $postgresifyCasts = [];

    protected $postgresifyPrimitiveCasts = ['array'];

    public function setAttribute($key, $value)
    {
        if (in_array($key, array_keys($this->postgresifyCasts))) {
	    if (!is_object($value) && in_array($this->postgresifyCasts[$key]['type'], $this->postgresifyPrimitiveCasts) {
		$value = self::mutateToPgArray($value);
	    }
	}
        return parent::setAttribute($key, $value);
    }

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if (!is_null($value) && in_array($key, array_keys($this->postgresifyCasts))) {
	    if (in_array($this->postgresifyCasts[$key]['type'], $this->postgresifyPrimitiveCasts) {
		return self::accessPgArray($value);
	    }
            $postgresifyTypeCaster = new PostgresifyTypeCaster();
            return $postgresifyTypeCaster->cast(
                $key,
                $value,
                $this->postgresifyCasts[$key]
            );
        }
        
        return $value;
    }
}
