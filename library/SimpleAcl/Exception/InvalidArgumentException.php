<?php
namespace SimpleAcl\Exception;

use InvalidArgumentException as SplException;
use SimpleAcl\Exception\ExceptionInterface as Exception;

/**
 * Class InvalidArgumentException
 *
 * @package SimpleAcl\Exception
 */
class InvalidArgumentException extends SplException implements Exception
{

}