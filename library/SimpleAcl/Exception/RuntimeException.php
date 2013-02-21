<?php
namespace SimpleAcl\Exception;

use RuntimeException as SplException;
use SimpleAcl\Exception\ExceptionInterface as Exception;

class RuntimeException extends SplException implements Exception
{

}