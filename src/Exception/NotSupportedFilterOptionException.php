<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Exception;

use InvalidArgumentException;
use Throwable;

final class NotSupportedFilterOptionException extends InvalidArgumentException
{
    /**
     * @param string $optionName Option name in filter.
     * @param string $driverName Driver name of database.
     */
    public function __construct(string $optionName, string $driverName, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("\$$optionName option is not supported when using $driverName driver.", $code, $previous);
    }
}
