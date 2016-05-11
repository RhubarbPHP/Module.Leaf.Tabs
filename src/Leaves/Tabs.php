<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Leaf\Tabs\Leaves;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;

class Tabs extends Leaf
{
    protected $tabs = [];

    /**
     * @var TabsModel
     */
    protected $model;

    /**
     * When true normal filtering of the collection based on the selected tab is disabled.
     *
     * Essential for getting potential counts of tabs for example.
     *
     * @var bool
     */
    protected $gettingUnfilteredCollection = false;

    /**
     * @var Event
     */
    public $selectedTabChangedEvent;

    /**
     * Raised if the selected tab has changed to indicate that other elements on the page might be
     * updated by this change.
     *
     * Used by other leaves such as the Table leaf
     *
     * @var Event
     */
    public $refreshesPageCollectionEvent;

    /**
     * Raised to get a Collection object for potentially obtaining a count for each tab.
     *
     * Note that this is not leveraged by the standard TabView.
     *
     * @var Event
     */
    public $getCollectionEvent;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->selectedTabChangedEvent = new Event();
        $this->getCollectionEvent = new Event();

        $this->refreshesPageCollectionEvent = $this->selectedTabChangedEvent;
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->getCollectionEvent->attachHandler(function(){
            $this->gettingUnfilteredCollection = true;
            $collection = $this->getCollectionEvent->raise();
            $this->gettingUnfilteredCollection = false;
            return $collection;
        });
    }

    public function setTabDefinitions($tabs = [])
    {
        $this->tabs = $tabs;
        $this->model->tabs = $this->getInflatedTabDefinitions();
    }

    public function getSelectedTab()
    {
        if ($this->model->selectedTab !== null) {
            return $this->getTabByIndex($this->model->selectedTab);
        }

        return null;
    }

    public function getTabDefinitions()
    {
        return $this->tabs;
    }

    public function getTabByIndex($tabIndex)
    {
        $tabs = $this->getInflatedTabDefinitions();

        return $tabs[$tabIndex];
    }

    protected final function getInflatedTabDefinitions()
    {
        $tabs = $this->inflateTabDefinitions();
        $this->markSelectedTab($tabs);

        return $tabs;
    }

    protected function inflateTabDefinitions()
    {
        $inflatedTabDefinitions = [];

        foreach ($this->tabs as $key => $value) {
            if ($value instanceof TabDefinition) {
                $inflatedTabDefinitions[] = $value;
            } elseif (is_string($key)) {
                $inflatedTabDefinitions[] = new TabDefinition($key, $value);
            }
        }

        return $inflatedTabDefinitions;
    }

    protected function markSelectedTab(&$inflatedTabDefinitions)
    {
        if ($this->model->selectedTab !== null) {
            $inflatedTabDefinitions[$this->model->selectedTab]->selected = true;
        }
    }

    /**
     * Set's the selected tab to the one indexed by $index
     *
     * Triggers the SelectedTabChanged event.
     *
     * @param $tabIndex
     */
    public function selectTabByIndex($tabIndex)
    {
        $this->model->selectedTab = $tabIndex;

        $this->selectedTabChangedEvent->raise($tabIndex);

        $this->onSelectedTabChanged($tabIndex);
    }

    /**
     * Override to perform actions when the selected tab changes.
     */
    protected function onSelectedTabChanged($tabIndex)
    {

    }

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return TabsView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new TabsModel();
        $model->tabSelectedEvent->attachHandler(function ($tabIndex) {
            $this->selectTabByIndex($tabIndex);
        });

        return $model;
    }
}