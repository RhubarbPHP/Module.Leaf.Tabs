<?php

namespace Rhubarb\Leaf\Tabs\Leaves;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\UrlStateLeafModel;

class TabsModel extends UrlStateLeafModel
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

    /**
     * Raised when the interface needs a count of items under a tab.
     *
     * Optional - if not connected, a count will simply not be shown.
     *
     * @var Event
     */
    public $getCountForTabEvent;


    /**
     * Raised to get a Collection object for potentially obtaining a count for each tab.
     *
     * @var Event
     */
    public $getCollectionEvent;

    public function __construct()
    {
        parent::__construct();

        $this->getCollectionEvent = new Event();
        $this->tabSelectedEvent = new Event();
        $this->getCountForTabEvent = new Event();
    }

    protected function getExposableModelProperties()
    {
        $list = parent::getExposableModelProperties();
        $list[] = "selectedTab";

        return $list;
    }

}