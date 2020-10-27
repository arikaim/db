<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits;

use Arikaim\Core\Utils\DateTime;

/**
 * Date search query
 * 
*/
trait DateSearch
{    
    /**
     * Get year query scope
     *
     * @param string $columnName
     * @param string $year
     * @return Builder
     */
    public function scopeYear($query, $columnName, $year)
    {     
        $start = DateTime::toTimestamp($year . '-01-01T00:00:00.0');
        $end = DateTime::toTimestamp($year . '-12-31T12:59:59.0');
       
        return $query->where($columnName,'>',$start)->where($columnName,'<',$end);
    }

    /**
     * Get month query scope
     *
     * @param string $columnName
     * @param string $year
     * @return Builder
     */
    public function scopeMonth($query, $columnName, $month, $year = null)
    {     
        $year = (empty($year) == true) ? DateTime::getYear() : $year;
        $lastDay = DateTime::getLastDay($month);
        
        $start = DateTime::toTimestamp($year . '-' . $month . '-01T00:00:00.0');
        $end = DateTime::toTimestamp($year . '-' . $month . '-' . $lastDay . 'T12:59:59.0');
       
        return $query->where($columnName,'>',$start)->where($columnName,'<',$end);
    }
}
