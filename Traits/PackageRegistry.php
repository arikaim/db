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

/**
 * Package Registry 
 *      
*/
trait PackageRegistry
{    
    /**
     * Get package name column(attribute)
     *
     * @return string
     */
    public function getPackageNameColumn()
    {
        return $this->pckageColumnName ?? 'name';
    }

    /**
     * Get package
     * 
     * @param string $name
     * @return array
     */
    public function getPackage($name)
    {
        $model = $this->findByColumn($name,$this->getPackageNameColumn());  
        
        return (\is_object($model) == true) ? $model->toArray() : false;
    }

    /**
     * Return true if extension record exist.
     *
     * @param string $name
     * @return boolean
     */
    public function hasPackage($name)
    {
        $model = $this->where($this->getPackageNameColumn(),'=',$name)->first();     
          
        return \is_object($model);
    }

    /**
     * Set package status
     *
     * @param string $name
     * @param integer $status
     * @return boolean
    */
    public function setPackageStatus($name, $status)
    {
        $model = $this->findByColumn($name,$this->getPackageNameColumn());  

        return (\is_object($model) == true) ? $model->setStatus($status) : false;     
    }

    /**
     * get package status
     *
     * @param string $name
     * @return integer
    */
    public function getPackageStatus($name)
    {
        $model = $this->where($this->getPackageNameColumn(),'=',$name)->first();       

        return (\is_object($model) == false) ? 0 : $model->status;            
    }

    /**
     * Return package name
     * 
     * @param string $name
     * @param array $data
     * @return boolean
     */
    public function addPackage($name, array $data)
    {
        $model = $this->findByColumn($name,$this->getPackageNameColumn()); 
        if (\is_object($model) == true) {
            $result = $model->update($data);
            return ($result !== false);
        }
        $model = $this->create($data);

        return \is_object($model);
    }

    /**
     * Get Package version
     * 
     * @param string $name
     * @return boolean
     */
    public function removePackage($name)
    {
        if ($this->hasPackage($name) == true) {
            return $this->where($this->getPackageNameColumn(),'=',$name)->delete();
        }

        return true;
    }

    /**
     * Get packages list
     *
     * @param array $filter
     * @return array
    */
    public function getPackagesList($filter = [])
    {        
        $model = $this;
        foreach ($filter as $key => $value) {
            $model = $model->where($key,'=',$value);
        }
        $model = $model->orderBy('position')->orderBy('id');

        return $model->get()->keyBy($this->getPackageNameColumn());
    }

    /**
     * Set package as primary
     *
     * @param string $name
     * @return boolean
    */
    public function setPrimary($name)
    {
        $model = $this->findByColumn($name,$this->getPackageNameColumn());
        
        return (\is_object($model) == false) ? false : $model->setDefault();                    
    }

    /**
     * Return true if package is primary.
     *  
     * @param string $name
     * @return boolean|null
    */
    public function isPrimary($name)
    {
        $model = $this->findByColumn($name,$this->getPackageNameColumn());

        return (\is_object($model) == false) ? null : ($model->primary == 1);                
    }
}
