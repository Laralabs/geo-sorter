<?php
/**
 * @license The MIT License (MIT) See: LICENSE file
 * @copyright Copyright (c) 2019 Matt Clinton
 * @author Matt Clinton <matt@laralabs.uk>
 * @link https://github.com/Laralabs/geo-sorter
 */

namespace Laralabs\GeoSorter\Exceptions;

class PostcodeNotFoundException extends \Exception
{
    protected $message = 'The postcode passed to sortByPostcode() could not be found in the database';
}