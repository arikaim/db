<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Traits\Document;

/**
 * Document payments table trait
*/
trait DocumentPayments 
{ 
    /**
     * Get payments total
     * 
     * @param int $documentId
     * @return float
     */
    public function getPaymentsTotal(int $documentId): float
    {
        $total = $this->paymentsQuery($documentId)->sum('amount');
        
        return (\is_numeric($total) == false) ? 0.00 : (float)$total;
    }

    /**
     * Save document payment
     *
     * @param integer $documentId
     * @param float   $amount
     * @return Model|false
     */
    public function savePayment(int $documentId, float $amount)
    {
        $model = $this->create([
            'document_id' => $documentId,
            'amount'      => $amount
        ]); 
        
        return (\is_object($model) == true) ? $model : false;
    }

    /**
     * Pyaments items query
     *
     * @param Builder $query
     * @param integer|null $documentId
     * @return Builder
     */
    public function scopePaymentsQuery($query, ?int $documentId)
    {
        if (empty($documentId) == false) {
            return $query->where('document_id','=',$documentId);
        }
    
        return $query;
    }
}
