<?php

namespace Rhubarb\Leaf\Tabs\Examples\SearchPanelTabsExample;

use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;

class SearchPanelTabsExample extends Leaf
{
    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return SearchPanelTabsExampleView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        return new SearchPanelTabsExampleModel();
    }
}