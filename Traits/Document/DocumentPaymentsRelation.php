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
 * Document payments relation table trait
*/
trait DocumentPaymentsRelation 
{ 
    /**
     * Get document payments model class
     *
     * @return string|null
     */
    public function getDocumentPaymentsClass(): ?string
    {
        return $this->documentPaymentsModel ?? null;
    }

    /**
     * Document payments relation
     *
     * @return Relation|null
     */
    public function payments()
    {
        $class = $this->getDocumentPaymentsClass();
        if (empty($class) == true) {
            return null;
        }

        return $this->hasMany($class,'document_id');
    }

    /**
     * Get total payments
     *
     * @return float
     */
    public function getTotalPaid(): float
    {
        $result = $this->payments()->where('docuemnt','=',$this->document_id)->sum('amount');

        return (empty($result) == true) ? 0.00 : (float)$result;
    }

    /**
     * total_paid attribute
     *
     * @return float
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->getTotalPaid();
    }

    /**
     * Get payment due amount
     *
     * @return float
     */
    public function getPaymentsDue(): float
    {
        return ($this->getTotal() - $this->getTotalPaid());
    }

    /**
     * Return true if doc is paid
     *
     * @return boolean
     */
    public function isPaid(): bool
    {
       return ($this->getPaymentsDue() <= 0);
    }
}
