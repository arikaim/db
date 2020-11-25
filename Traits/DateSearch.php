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

use Arikaim\Core\Utils\TimePeriod;

/**
 * Date search query
 * 
*/
trait DateSearch
{    
    /**
     * Get year query scope
     *
     * @param Builder $query 
     * @param string $columnName
     * @param string|null $year
     * @return Builder
     */
    public function scopeYear($query, $columnName, $year = null)
    {     
        $period = TimePeriod::getYearPeriod($year);

        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }

    /**
     * Get month query scope
     *
     * @param Builder $query 
     * @param string $columnName
     * @param string|null $month
     * @param string|null $year
     * @return Builder
     */
    public function scopeMonth($query, $columnName, $month = null, $year = null)
    {     
        $period = TimePeriod::getMonthPeriod($month,$year);
       
        return $query->where($columnName,'>',$period['start'])->where($columnName,'<',$period['end']);
    }
}
