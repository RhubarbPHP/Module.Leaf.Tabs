<?php

namespace Rhubarb\Leaf\Tabs\Examples\SearchPanelTabsExample;

use Rhubarb\Crown\Deployment\DeploymentPackage;
use Rhubarb\Crown\Deployment\ResourceDeploymentPackage;
use Rhubarb\Leaf\Table\Leaves\Table;
use Rhubarb\Leaf\Tabs\Leaves\SearchPanelTabDefinition;
use Rhubarb\Leaf\Tabs\Leaves\SearchPanelTabs;
use Rhubarb\Leaf\Views\View;

class SearchPanelTabsExampleView extends View
{
    protected function createSubLeaves()
    {
        $this->registerSubLeaf(
            $this->search = new JobSearchPanel(),
            $this->tabs = new SearchPanelTabs("Search"),

            // Set up a table with an unfiltered collection - on render the filter for the
            // first tab will be applied.
            $this->table = new Table(Job::all())
        );

        $this->tabs->setTabDefinitions([
            new SearchPanelTabDefinition('Incoming', ['Status' => 'Incoming']),
            new SearchPanelTabDefinition('Outgoing', ['Status' => 'Outgoing']),
            new SearchPanelTabDefinition('Stale', ['Status' => 'Stale']),
            new SearchPanelTabDefinition('Sent', [
                'Status' => 'Outgoing',
                'Sent' => true
            ]),
        ]);

        $this->table->columns = [
            "JobID",
            "JobTitle",
            "Status",
            "Sent"
        ];

        $this->search->bindEventsWith($this->table);
        $this->search->bindEventsWith($this->tabs);
        $this->tabs->bindEventsWith($this->table);
    }

    protected function printViewContent()
    {
        print $this->search;
        print $this->tabs;
        print $this->table;
    }

    public function getDeploymentPackage()
    {
        $package = new ResourceDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__.'/Tabs.css';

        return $package;
    }
}