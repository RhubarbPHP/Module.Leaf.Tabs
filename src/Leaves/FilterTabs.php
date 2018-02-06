<?php

namespace Rhubarb\Leaf\Tabs\Leaves;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\SearchPanel\Leaves\SearchPanel;
use Rhubarb\Stem\Filters\AndGroup;

class FilterTabs extends Tabs
{

    protected function populateFilter()
    {
        if ($this->isSearchActive()){
            return null;
        }

        $tabs = $this->getInflatedTabDefinitions();
        $tab = $tabs[$this->model->selectedTab];

        if ($tab && $tab instanceof FilterTabDefinition) {
            return $tab->getFilter();
        }

        return null;
    }

    /**
     * If used in conjunction with a search panel we interact to show a search results tab.
     *
     * @var null
     */
    private $activeSearchPanel = null;

    protected function bindEvents(Leaf $presenter)
    {
        if (isset($presenter->getFilterEvent) && $presenter->getFilterEvent instanceof Event) {
            $presenter->getFilterEvent->attachHandler(
                function () {
                    return $this->populateFilter();
                }
            );
        }

        if (method_exists($presenter, "populateFilterGroup" )){
            $this->activeSearchPanel = $presenter;

            $presenter->searchedEvent->attachHandler(
                function () {
                    $this->model->tabs = null;
                    $this->reRender();
                }
            );
        }
    }

    protected function isSearchActive()
    {
        if ($this->activeSearchPanel){
            $filterGroup = new AndGroup();
            $this->activeSearchPanel->populatefilterGroup($filterGroup);
            
            if (count($filterGroup->getFilters()) > 0){
                return true;
            }
        }
        
        return false;
    }

    protected function inflateTabDefinitions()
    {
        $tabs = parent::inflateTabDefinitions();

        if ($this->activeSearchPanel){

            if ($this->isSearchActive()){
                $tabs[] = $searchTab = new SearchPanelTabDefinition("Search Results");
            }
        }

        return $tabs;
    }


    protected function onSelectedTabChanged($tabIndex)
    {
        parent::onSelectedTabChanged($tabIndex);

        if ($this->activeSearchPanel){
            $this->activeSearchPanel->setSearchControlValues([]);
        }
    }

    protected function markSelectedTab(&$inflatedTabDefinitions)
    {
        parent::markSelectedTab($inflatedTabDefinitions);

        foreach($inflatedTabDefinitions as $tabDefinition){

            if ($tabDefinition instanceof  SearchPanelTabDefinition) {
                $tabDefinition->selected = true;
                $this->model->selectedTab = count($inflatedTabDefinitions)-1;
            } else {
                $tabDefinition->selected = false;
            }
        }
    }


    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->getCountForTabEvent->clearHandlers();
        $this->model->getCountForTabEvent->attachHandler(function (TabDefinition $tab) {
            if (!$this->includeCountIfSupported) {
                return null;
            }

            $collection = $this->getCollectionEvent->raise();
            if ($collection) {
                if ($tab instanceof  FilterTabDefinition) {
                    $collection->clearFilter();
                    $collection->filter($tab->getFilter());
                }

                return count($collection);
            } else {
                return null;
            }
        });
    }
}