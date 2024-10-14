<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler\LikeFilterHandler;

use Yiisoft\Data\Db\Exception\NotSupportedFilterOptionException;
use Yiisoft\Data\Db\FilterHandler\Context;
use Yiisoft\Data\Db\FilterHandler\Criteria;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class MssqlLikeFilterHandler extends BaseLikeFilterHandler implements QueryFilterHandlerInterface
{
    public function __construct()
    {
        unset($this->escapingReplacements['\\']);
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        if ($filter->isCaseSensitive() === true) {
            throw new NotSupportedFilterOptionException(optionName: 'caseSensitive', driverName: 'sqlsrv');
        }

        return new Criteria(['LIKE', $filter->getField(), $this->prepareValue($filter->getValue())]);
    }
}
