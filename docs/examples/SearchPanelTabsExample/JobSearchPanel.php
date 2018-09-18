<?php

namespace Rhubarb\Leaf\Tabs\Examples\SearchPanelTabsExample;

use Rhubarb\Leaf\Controls\Common\Checkbox\Checkbox;
use Rhubarb\Leaf\Controls\Common\SelectionControls\DropDown\DropDown;
use Rhubarb\Leaf\SearchPanel\Leaves\SearchPanel;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\Group;

class JobSearchPanel extends SearchPanel
{
    protected function createSearchControls()
    {
        $status = new DropDown("Status");
        $status->setSelectionItems([
            ["", "Any Status"],
            ["Incoming"],
            ["Outgoing"],
            ["Stale"]
        ]);

        return [
            $status,
            new Checkbox("Sent")
        ];
    }

    public function populateFilterGroup(Group $filterGroup)
    {
        $searchValues = $this->getSearchControlValues();

        if ($searchValues["Status"]) {
            $filterGroup->addFilters(new Equals("Status", $searchValues["Status"]));
        }

        $filterGroup->addFilters(new Equals("Sent", (bool)$searchValues["Sent"]));
    }
}