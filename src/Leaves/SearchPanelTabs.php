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

class SearchPanelTabs extends Tabs
{
    /**
     * Raised to allow the host to respond when a search bound tab is selected.
     *
     * @var Event
     */
    public $searchBoundTabSelectedEvent;

    /**
     * Raised to allow the host to provide search control values
     *
     * @var Event
     */
    public $getSearchControlValuesEvent;


    /**
     * Raised to allow the host to process the search values set by a tab.
     *
     * @var Event
     */
    public $setSearchControlValuesEvent;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->searchBoundTabSelectedEvent = new Event();
        $this->getSearchControlValuesEvent = new Event();
    }

    protected function onSelectedTabChanged($tabIndex)
    {
        parent::onSelectedTabChanged($tabIndex);

        $this->inflatedTabs = null;

        // If the tab that's been selected has control values attached, throw an event to say so.
        $tab = $this->getTabByIndex($tabIndex);

        if ($tab instanceof SearchPanelTabDefinition) {
            $this->searchBoundTabSelectedEvent->raise($tab);
        }
    }

    protected function inflateTabDefinitions()
    {
        $inflatedTabDefinitions = [];

        foreach ($this->tabs as $key => $value) {
            if ($value instanceof TabDefinition) {
                $inflatedTabDefinitions[] = $value;
            } elseif (is_string($key)) {
                if (is_array($value)) {
                    $inflatedTabDefinitions[] = new SearchPanelTabDefinition($key, $value);
                } else {
                    $inflatedTabDefinitions[] = new TabDefinition($key, $value);
                }
            }
        }

        $currentSearchValues = $this->getSearchControlValuesEvent->raise();

        $anySelected = false;

        if ($currentSearchValues !== null) {
            $x = -1;
            foreach ($inflatedTabDefinitions as $tab) {
                $x++;
                $same = true;

                foreach ($tab->data as $key => $value) {
                    if (!isset($currentSearchValues[$key])) {
                        $same = false;
                        break;
                    }

                    if (is_object($value) ||is_array($value)){
                        if (json_encode($currentSearchValues[$key]) != json_encode($value)){
                            $same = false;
                            break;
                        }
                    } else {
                        if ($currentSearchValues[$key] !== $value) {
                            $same = false;
                            break;
                        }
                    }
                }

                foreach ($currentSearchValues as $key => $value) {
                    if (!isset($tab->data[$key])) {
                        if ($value !== false && $value !== null && $value !== "") {
                            $same = false;
                            break;
                        }

                        continue;
                    }

                    if (is_object($value) ||is_array($value)) {
                        if (json_encode($tab->data[$key]) != json_encode($value)) {
                            $same = false;
                            break;
                        }
                    } else {
                        if ($tab->data[$key] !== $value) {
                            $same = false;
                            break;
                        }
                    }
                }

                if ($same) {
                    $anySelected = true;
                    $this->model->selectedTab = $x;;
                }
            }
        } else {
            $currentSearchValues = [];
        }

        if (!$anySelected) {
            $inflatedTabDefinitions[] = $searchResults = new SearchResultsTabDefinition("Search Results");
            $this->model->selectedTab = sizeof($inflatedTabDefinitions) - 1;
            $searchResults->data = $currentSearchValues;
        }

        return $inflatedTabDefinitions;
    }

    protected function bindEvents(Leaf $presenter)
    {
        if (property_exists($presenter, "searchedEvent" )){
            $presenter->searchedEvent->attachHandler(
                function () {
                    $this->reRender();
                }
            );
        }

        if (method_exists($presenter, "setSearchControlValues" )){
            $this->selectedTabChangedEvent->attachHandler(
                function ($index) use ($presenter) {
                    /**
                     * @var $tab SearchPanelTabDefinition
                     */
                    $tab = $this->getTabByIndex($index);
                    $presenter->setSearchControlValues($tab->data);
                }
            );
        }
    }
}