<?php

namespace AppBundle\Helper;

use AppBundle\Exception\InvalidDateRangeException;

class AdditionalFunction
{
    /**
     * @param string $date
     * @return \DateTime|InvalidDateRangeException
     */
    public function validateDateTime($date)
    {
        $dateTimeClass = date_create_from_format('Y-m-d H:i:s', $date);

        if (!$dateTimeClass instanceof \DateTime) {
            throw new InvalidDateRangeException('Date fields must be format \'Y-m-d H:i:s\'');
        }
        return $dateTimeClass;
    }    
}
