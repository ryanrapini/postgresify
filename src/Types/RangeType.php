<?php

namespace Aejnsn\Postgresify\Types;

abstract class RangeType extends AbstractType
{

    /**
     * @var mixed The lower bound value.
     */
    protected $lowerBound;
    /**
     * @var bool Whether the lower bound value is inclusive.
     */
    protected $lowerBoundInclusive;
    /**
     * @var mixed The upper bound value.
     */
    protected $upperBound;
    /**
     * @var bool Whether the upper bound value is inclusive.
     */
    protected $upperBoundInclusive;

    public function __construct($lowerBound, $upperBound, $lowerBoundInclusive = true, $upperBoundInclusive = true)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->lowerBoundInclusive = $lowerBoundInclusive;
        $this->upperBoundInclusive = $upperBoundInclusive;
    }

    /**
     * Output the type to a string, in the PostgreSQL preferred format.
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->lowerBoundInclusive ? '[' : '(') .
            $this->lowerBound . ',' . $this->upperBound .
            ($this->upperBoundInclusive ? ']' : ')');
    }

    public function fromPgValues($pgValue)
    {
        if (empty($pgValue)
            || ($pgValue[0] != '[' && $pgValue[0] != '(')
            || ($pgValue[strlen($pgValue) - 1] != ']' && $pgValue[strlen($pgValue) - 1] != ')')
        ) {
            throw new \Exception("Not valid Postgres numrange data - bounds");
        }
        if ($pgValue[0] == '[') {
            $this->lowerBoundInclusive = true;
        } else {
            $this->lowerBoundInclusive = false;
        }

        if ($pgValue[strlen($pgValue) - 1] == ']') {
            $this->upperBoundInclusive = true;
        } else {
            $this->upperBoundInclusive = false;
        }

        $values = explode(',', substr($pgValue, 1, -1));

        if (count($values) != 2) {
            throw new \Exception("Not valid Postgres numrange data - values");
        }
        $this->lowerBound = $values[0];
        $this->upperBound = $values[1];

        return $this;
    }

    /**
     * Return boolean as to whether the lower bound is inclusive.
     *
     * @return mixed
     */
    protected function getIsLowerBoundInclusive()
    {
        return $this->lowerBoundInclusive;
    }

    /**
     * Returns the range's lower bound.
     *
     * @return mixed
     */
    protected function getLowerBound()
    {
        return $this->lowerBound - 0;
    }

    /**
     * Sets the lower bound of the range.
     *
     * @param $value
     * @param bool $inclusive
     */
    protected function setLowerBound($value, $inclusive = false)
    {
        $this->lowerBoundInclusive = $inclusive;
        $this->lowerBound = $value;
    }

    /**
     * Returns the range's upper bound.
     *
     * @return mixed
     */
    protected function getUpperBound()
    {
        return ($this->getIsUpperBoundInclusive()) ? $this->upperBound : ($this->upperBound - 1);
    }

    /**
     * Sets the upper bound of the range.
     *
     * @param $value
     * @param bool $inclusive
     */
    protected function setUpperBound($value, $inclusive = false)
    {
        $this->upperBoundInclusive = $inclusive;
        $this->upperBound = $value;
    }

    /**
     * Return boolean as to whether the upper bound is inclusive.
     *
     * @return mixed
     */
    protected function getIsUpperBoundInclusive()
    {
        return $this->upperBoundInclusive;
    }

    /**
     * Sets the lower inclusive bound of the range.
     *
     * @param $value
     */
    protected function setLowerBoundInclusive($value)
    {
        $this->setLowerBound($value, true);
    }

    /**
     * Sets the upper inclusive bound of the range.
     *
     * @param $value
     */
    protected function setUpperBoundInclusive($value)
    {
        $this->setUpperBound($value, true);
    }
}
