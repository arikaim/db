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
    public function getDocumentNumberColumn()
    {
        return (isset($this->documentNumberColumn) == true) ? $this->documentNumberColumn : 'document_number';
    } 

    /**
     * Get document number unique index columns
     *
     * @return string
     */
    public function getDocumentNumberUniqueIndex()
    {
        return (isset($this->documentNumberUniqueIndex) == true) ? $this->documentNumberUniqueIndex : null;
    } 

    /**
     * Get label
     *
     * @return string
     */
    public function getDocumentNumberLabel()
    {
        return (isset($this->documentNumberLabel) == true) ? $this->documentNumberLabel : '';
    } 

    /**
     * Get next document number
     *     
     * @return integer
     */
    public function getNextDocumentNumber($filterColumnValue = null)
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
     * @param string|null $filterColumnValue
     * @return boolean
     */
    public function isValidDocumentNumber($documentNumber = null, $filterColumnValue = null)
    {
        $columnName = $this->getDocumentNumberColumn();
        $columnValue = (isset($this->attributes[$columnName]) == true) ? $this->attributes[$columnName] : $documentNumber;

        $indexColumn = $this->getDocumentNumberUniqueIndex();
        $filterColumnValue = (empty($filterColumnValue) == true) ? $this->{$indexColumn} : $filterColumnValue;

        $model = $this->where($columnName,'=',$columnValue)->where($indexColumn,'=',$filterColumnValue)->first();
        
        return !is_object($model);
    } 

    /**
     * Get document number
     *
     * @param string $prefix
     * @return string|null
     */
    public function getDocumentNumber($prefix = '')
    {
        $columnName = $this->getDocumentNumberColumn();
        $label = $this->getDocumentNumberLabel();
        $documentNumber = (isset($this->attributes[$columnName]) == true) ? $this->attributes[$columnName] : null;
     
        return (empty($documentNumber) == false) ? sprintf($label . '1%05d' . $prefix,$documentNumber) : null;       
    }   
}
