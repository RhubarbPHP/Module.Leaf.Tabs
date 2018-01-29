<?php

namespace Rhubarb\Leaf\Tabs\Leaves;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\Leaf;

class FilterTabs extends Tabs
{
    protected function populateFilter()
    {
        $tab = $this->model->tabs[$this->model->selectedTab];
        if ($tab instanceof FilterTabDefinition) {
            return $tab->getFilter();
        }

        return null;
    }

    protected function bindEvents(Leaf $presenter)
    {
        if (isset($presenter->getFilterEvent) && $presenter->getFilterEvent instanceof Event) {
            $presenter->getFilterEvent->attachHandler(
                function () {
                    return $this->populateFilter();
                }
            );
        }
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();

        $this->model->getCountForTabEvent->clearHandlers();
        $this->model->getCountForTabEvent->attachHandler(function (FilterTabDefinition $tab) {
            if (!$this->includeCountIfSupported) {
                return null;
            }
            $collection = $this->getCollectionEvent->raise();
            if ($collection) {
                $collection->clearFilter();
                $collection->filter($tab->getFilter());
                return count($collection);
            } else {
                return null;
            }
        });
    }
}