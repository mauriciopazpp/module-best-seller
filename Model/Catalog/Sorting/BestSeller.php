<?php
/**
 * New option to sort by Best Seller using report sql tables
 * 
 * New class as vendor/magento/module-page-builder/Model/Catalog/Sorting
 * 
 * @author Mauricio Paz Pacheco
 * @copyright Copyright © 2020 Mpaz. All rights reserved.
 * @package Mpaz_BestSellerProducts
 */

declare(strict_types=1);

namespace Mpaz\BestSellerProducts\Model\Catalog\Sorting;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\DB\Select;
use Magento\Framework\Phrase;
use Magento\PageBuilder\Model\Catalog\Sorting\OptionInterface;

class BestSeller implements OptionInterface
{
    /**
     * Used by magento to treat phrases
     *
     * @var Phrase
     */
    private $label;

    /**
     * Direction of the query
     *
     * @var Select
     */
    private $sortDirection;

    /**
     * Secondary direction
     *
     * @var Select
     */
    private $secondarySortDirection;

    /**
     * Construct
     * 
     * @param string $label
     * @param string $sortDirection
     * @param string $secondarySortDirection
     */
    public function __construct(
        string $label,
        string $sortDirection = Select::SQL_ASC,
        ?string $secondarySortDirection = null
    ) {
        $this->label = $label;
        $this->sortDirection = $sortDirection;
        $this->secondarySortDirection = $secondarySortDirection ?? $sortDirection;
    }

    /**
     * Sort by best seller, using Report database queries,
     * !!Needs to run the reports to make it works!!
     *
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection): Collection
    {
        $collection->getSelect()->reset(Select::ORDER);

        $collection->getSelect()
            ->joinLeft(
                ['sales_bestsellers_aggregated_daily'],
                'e.entity_id = sales_bestsellers_aggregated_daily.product_id',
                ['sales_bestsellers_aggregated_daily.qty_ordered'])
            ->group("e.entity_id")
            ->order("sales_bestsellers_aggregated_daily.qty_ordered $this->sortDirection");
        $collection->addAttributeToSort('entity_id', $this->secondarySortDirection);
        return $collection;
    }

    /**
     * Get the phrase and translate it
     *
     * @return Phrase
     */
    public function getLabel(): Phrase
    {
        return __($this->label);
    }
}
