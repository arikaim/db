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

use \Illuminate\Database\QueryException;
use Exception;

/**
 * Query errors trait
*/
trait Errors 
{    
    /**
     * Get error messages
     *
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages ?? [
            23000 => "Can't delete item, data item is used."
        ];
    }

    /**
     * Get default error message
     *
     * @return string
     */
    public function getDefaultError(): string
    {
        return $this->defaultError ?? 'Db query error';
    }
    
    /**
     * Get error message
     *
     * @param integer $code
     * @return string
     */
    public function getErrorMessage(int $code): string
    {
        return $this->getErrorMessages()[$code] ?? $this->getDefaultError();
    }

    /**
     * Set error msg
     *
     * @param string|null $msg
     * @return void
     */
    public function setError(?string $msg = null): void
    {
        $this->error = $msg;
    }

    /**
     * Get error msg
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error ?? '';
    }

    /**
     * Get sql error
     *
     * @return string
     */
    public function getSqlError(): string
    {
        return $this->sqlError ?? '';
    }

    /**
     * Set sql error
     *
     * @param string|null $msg
     * @return void
     */
    public function setSqlError(?string $msg = null): void
    {
        $this->sqlError = $msg;
    }

    /**
     * Delete record
     *
     * @param bool $throwException
     * @return bool|null
     * @throws Exception
     */
    public function delete(bool $throwException = true)
    {
        try {
            $this->setError();
            $this->setSqlError();

            return parent::delete();

        } catch (QueryException $e) {
            $errorMessage = $this->getErrorMessage((int)$e->getCode());
            $this->setSqlError($e->getMessage());
            $this->setError($errorMessage);
            if ($throwException == true) {
                throw new Exception($errorMessage,1);           
            }

            return false;
        }
    }
}
