<?php
namespace SimpleAcl\Exception;

use \InvalidArgumentException as SplException;
use SimpleAcl\Exception\ExceptionInterface as Exception;

class InvalidArgumentException extends SplException implements Exception
{

}