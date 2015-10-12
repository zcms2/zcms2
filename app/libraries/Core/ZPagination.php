<?php

namespace ZCMS\Core;

use Phalcon\Di;
use Phalcon\Db;
use Phalcon\Paginator\Adapter\Model as PaginationModel;
use Phalcon\Paginator\Adapter\NativeArray as PaginationNativeArray;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginationQueryBuilder;
use Phalcon\Mvc\Model\Resultset\Simple as ResultSet;

/**
 * Class ZPagination
 * Helper coder pagination page view
 *
 * @package ZCMS
 */
class ZPagination
{

    const Z_PAGINATION_OBJECT = '0';
    const Z_PAGINATION_ARRAY = '1';

    /**
     * Get pagination from query builder
     *
     * @param \Phalcon\Mvc\Model\Query\BuilderInterface $queryBuilder
     * @param int $paginationLimit
     * @param int $currentPage
     * @return \stdClass
     */
    public static function getPaginationQueryBuilder($queryBuilder, $paginationLimit, $currentPage)
    {
        $pagination = new PaginationQueryBuilder([
            'builder' => $queryBuilder,
            'limit' => self::checkInt($paginationLimit),
            'page' => self::checkInt($currentPage)
        ]);
        return $pagination->getPaginate();
    }

    /**
     * Get pagination from phalcon model
     *
     * @param \Phalcon\Mvc\Model
     * @param int $paginationLimit
     * @param int $currentPage
     * @return \stdClass
     */
    public static function getPaginationModel($model, $paginationLimit, $currentPage)
    {
        $pagination = new PaginationModel([
            'data' => $model,
            'limit' => self::checkInt($paginationLimit),
            'page' => self::checkInt($currentPage)
        ]);
        return $pagination->getPaginate();
    }

    /**
     * Get pagination from array
     *
     * @param array $arrayRow
     * @param int $paginationLimit
     * @param int $currentPage
     * @return \stdClass
     */
    public static function getPaginationNativeArray($arrayRow, $paginationLimit, $currentPage)
    {
        $pagination = new PaginationNativeArray([
            'data' => $arrayRow,
            'limit' => self::checkInt($paginationLimit),
            'page' => self::checkInt($currentPage)
        ]);
        return $pagination->getPaginate();
    }

    /**
     * Get pagination from total number item and current page
     *
     * @param mixed $items
     * @param int $totalItem
     * @param int $limit
     * @param int $currentPage
     * @return \stdClass
     */
    public static function getPaginationFromTotalAndCurrentPage($items, $totalItem, $limit, $currentPage)
    {
        $pagination = new \stdClass();
        $pagination->items = $items;
        $pagination->current = abs((int)$currentPage);
        $pagination->total_items = $totalItem;
        $pagination->total_pages = ceil($totalItem / $limit);
        if ($pagination->current > $pagination->total_pages) {
            $pagination->current = 1;
        }
        if ($currentPage < $pagination->total_pages) {
            $pagination->next = $pagination->current + 1;
        } else {
            $pagination->next = $pagination->total_pages;
        }
        $pagination->last = $pagination->total_pages;
        if ($pagination->total_pages > 0) {
            $pagination->first = 1;
        } else {
            $pagination->first = 0;
        }
        if ($pagination->current > 1) {
            $pagination->before = $pagination->current - 1;
        } else {
            $pagination->before = 0;
        }
        return $pagination;
    }

    /**
     * Get pagination from raw sql
     *
     * @param $rawSQL
     * @param $paginationLimit
     * @param $currentPage
     * @param \Phalcon\Mvc\Model|\stdClass $object
     * @return \stdClass
     */
    public static function getPaginationFromRawSQL($rawSQL, $paginationLimit, $currentPage, $object)
    {
        $result = new \stdClass();
        $result->before = 1;
        $result->first = 1;
        $result->next = 0;
        $result->current = 1;
        $result->total_pages = 0;
        $result->total_items = 0;

        $currentPage = self::checkInt($currentPage);
        $paginationLimit = self::checkInt($paginationLimit);

        /**
         * @var mixed $db
         */
        $db = DI::getDefault()->get('db');
        try {
            $totalSQL = "SELECT count(*) AS total FROM ({$rawSQL}) AS Temp";
            $totalItem = $db->fetchOne($totalSQL, Db::FETCH_NUM);
            if (count($totalItem) >= 0) {
                $totalItem = $totalItem[0];
                if ($totalItem[0] >= 0) {
                    $result = new \stdClass();
                    $totalPage = ceil($totalItem / $paginationLimit);
                    $result->first = 1;
                    $result->current = $currentPage;
                    if ($currentPage < $totalPage) {
                        $result->next = $currentPage + 1;
                    } else {
                        $result->next = $totalPage;
                    }
                    $result->last = $totalPage;
                    $result->total_pages = $totalPage;
                    $result->total_items = $totalItem;
                    $rawSQL = $rawSQL . ' LIMIT ' . $paginationLimit . ' OFFSET ' . ($currentPage - 1) * $paginationLimit;
                    $result->items = new ResultSet(null, $object, $db->query($rawSQL));
                    return $result;
                }
            }
        } catch (\Exception $e) {
            die('SQL Error: ' . $rawSQL);
        }

        return $result;
    }

    /**
     * Check Int (For service not support int, CFLAGS="-O2 -g -fomit-frame-pointer -DPHALCON_RELEASE")
     *
     * @param $number
     * @return int
     */
    public static function checkInt($number)
    {
        $number = (int)$number;
        return $number < 1 ? 1 : $number;
    }
} 