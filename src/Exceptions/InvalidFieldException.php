<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter\Exceptions;

use Throwable;

class InvalidFieldException extends \Exception
{
    public function __construct($value, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (is_string($value) === false) {
            $this->message = 'Invalid postcode_field configuration value "' . $value . '", it must be a string.';
        } else {
            $this->message = 'Postcode field "' . $value . '" not found on item in collection';
        }
    }
}
