<?php

namespace Rhubarb\Leaf\Tabs\Leaves;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\LeafModel;

class TabsModel extends LeafModel
{
    /**
     * The name/index of the currently selected tab.
     *
     * @var string
     */
    public $selectedTab = 0;

    /**
     * The tabs to display
     *
     * @var TabDefinition[]
     */
    public $tabs = [];

    /**
     * Raised when the user selects a tab.
     *
     * @var Event
     */
    public $tabSelectedEvent;

    public function __construct()
    {
        parent::__construct();

        $this->tabSelectedEvent = new Event();
    }
}