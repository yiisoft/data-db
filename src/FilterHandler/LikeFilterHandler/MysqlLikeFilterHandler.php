<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler\LikeFilterHandler;

use Yiisoft\Data\Db\FilterHandler\Context;
use Yiisoft\Data\Db\FilterHandler\Criteria;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\FilterInterface;

final class MysqlLikeFilterHandler extends BaseLikeFilterHandler implements QueryFilterHandlerInterface
{
    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Like $filter */

        if ($filter->isCaseSensitive() !== true) {
            return new Criteria(['LIKE', $filter->getField(), $this->prepareValue($filter->getValue())]);
        }

        return new Criteria([
            'LIKE BINARY',
            $filter->getField(),
            $this->prepareValue($filter->getValue(), escape: true),
        ]);
    }
}
