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

use Arikaim\Core\Interfaces\Reports\ReportInterface;
use Arikaim\Core\Utils\TimePeriod;

/**
 * Report data trait
 * 
*/
trait ReportData
{    
    /**
     * Data scope per period
     *
     * @param Builder $query    
     * @param string $periodType   
     * @param integer|null $day
     * @param integer|null $month
     * @param integer|null $year
     * @return Builder
     */
    public function scopeReportDataQuery(
        $query,  
        string $periodType,
        ?int $day = null, 
        ?int $month = null, 
        ?int $year = null
    )
    {
        switch ($periodType) {
            case ReportInterface::CALC_PERIOD_DAILY:      
                $period = TimePeriod::getDayPeriod($day,$month,$year);     
                break;
        
            case ReportInterface::CALC_PERIOD_MONTHLY:     
                $period = TimePeriod::getMonthPeriod($month,$year);       
                break;
    
            case ReportInterface::CALC_PERIOD_YEARLY:   
                $period = TimePeriod::getYearPeriod($month,$year);       
                break;           
        }

        return $query                   
            ->where('date_created','>=',$period['start'])
            ->where('date_created','<=',$period['end']);
    } 

    /**
     * Get report data
     *    
     * @param string $period
     * @param integer|null $day
     * @param integer|null $month
     * @param integer|null $year
     * @return array
     */
    public function getReportData(
        string $period, 
        ?int $day = null, 
        ?int $month = null, 
        ?int $year = null
    ): array
    {
        return $this->reportDataQuery($period,$day,$month,$year)->get()->toArray();       
    }
}
