<?php

namespace Rhubarb\Leaf\Tabs\Leaves;

use Rhubarb\Stem\Filters\Filter;

class FilterTabDefinition extends TabDefinition
{
    /**
     * @var Filter
     */
    private $filter;

    public function __construct($label, Filter $filter)
    {
        parent::__construct($label);

        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getCount()
    {
        return parent::getCount();
    }
}