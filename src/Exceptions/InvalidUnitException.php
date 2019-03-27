<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter\Exceptions;

use Throwable;

class InvalidUnitException extends \Exception
{
    public function __construct($value, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->message = 'Invalid distance_unit configuration value "' . $value . '". Supported values are metres, kilometres or miles.';
    }
}