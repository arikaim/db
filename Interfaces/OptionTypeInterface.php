<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Db\Interfaces;

/**
 * Db option type interface
 */
interface OptionTypeInterface
{        
    const TEXT          = 0;
    const CHECKBOX      = 1;
    const DROPDOWN      = 2;
    const TEXT_AREA     = 3;
    const RELATION      = 4;
    const NUMBER        = 5;
    const IMAGE         = 6;
    const PRICE         = 7;
    const FILE          = 8;
    const MARKDOWN      = 9;
    const DATE          = 10;
    const TIME_INTERVAL = 11;
    const USER_GROUP    = 12;
    const PERMISSION    = 13;

    const FILE_DATA_SOURCE     = 'file';
    const URL_DATA_SOURCE      = 'url';
    const CALLABLE_DATA_SOURCE = 'callable';
}
