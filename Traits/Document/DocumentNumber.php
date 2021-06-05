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
 * Document number table trait
*/
trait DocumentNumber 
{ 
    /**
     * Boot trait
     *
     * @return void
     */
    public static function bootDocumentNumber()
    {
        static::creating(function($model) {          
            $columnName = $model->getDocumentNumberColumn();
            if (empty($model->$columnName) == true) {  
                $model->attributes[$columnName] = $model->getNextDocumentNumber();
            }           
        });
    }

    /**
     * Get document number column
     *
     * @return string
     */
    public function getDocumentNumberColumn(): string
    {
        return $this->documentNumberColumn ?? 'document_number';
    } 

    /**
     * Get document number unique index columns
     *
     * @return string|null
     */
    public function getDocumentNumberUniqueIndex(): ?string
    {
        return $this->documentNumberUniqueIndex ?? null;
    } 

    /**
     * Get label
     *
     * @return string
     */
    public function getDocumentNumberLabel(): string
    {
        return $this->documentNumberLabel ?? '';
    } 

    /**
     * Get next document number
     *     
     * @param mixed|null $filterColumnValue
     * @return integer
     */
    public function getNextDocumentNumber($filterColumnValue = null): int
    {
        $columnName = $this->getDocumentNumberColumn();
        $indexColumn = $this->getDocumentNumberUniqueIndex();
        $filterColumnValue = (empty($filterColumnValue) == true) ? $this->{$indexColumn} : $filterColumnValue;
      
        $model = (empty($indexColumn) == false) ? $this->where($indexColumn,'=',$filterColumnValue) : $this;     
        $max = $model->max($columnName);

        return (empty($max) == true) ? 1 : ($max + 1); 
    }

    /**
     * Return true if document number is valid
     *
     * @param integer|null $documentNumber
     * @param mixed|null $filterColumnValue
     * @return boolean
     */
    public function isValidDocumentNumber(?int $documentNumber = null, $filterColumnValue = null): bool
    {
        $columnName = $this->getDocumentNumberColumn();
        $columnValue = (isset($this->attributes[$columnName]) == true) ? $this->attributes[$columnName] : $documentNumber;

        $indexColumn = $this->getDocumentNumberUniqueIndex();
        $filterColumnValue = (empty($filterColumnValue) == true) ? $this->{$indexColumn} : $filterColumnValue;

        $model = $this->where($columnName,'=',$columnValue)->where($indexColumn,'=',$filterColumnValue)->first();
        
        return (\is_object($model) == false);
    } 

    /**
     * Get document number
     *
     * @param string $prefix
     * @return string|null
     */
    public function getDocumentNumber(string $prefix = ''): ?string
    {
        $columnName = $this->getDocumentNumberColumn();      
        $documentNumber = $this->attributes[$columnName] ?? null;
     
        return (empty($documentNumber) == false) ? $this->filterColumnValue($documentNumber,$prefix) : null;       
    }   

    /**
     * Print doc number
     *
     * @param integer $number
     * @param string $prefix
     * @return string
     */
    public function printDocumentNumber(int $number, string $prefix = ''): string
    {
        $label = $this->getDocumentNumberLabel();

        return \sprintf($label . '%012d' . $prefix,$number);
    }
}
