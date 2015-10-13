<?php
namespace SimpleAcl\Exception;

use RuntimeException as SplException;
use SimpleAcl\Exception\ExceptionInterface as Exception;

/**
 * Class RuntimeException
 *
 * @package SimpleAcl\Exception
 */
class RuntimeException extends SplException implements Exception
{

}