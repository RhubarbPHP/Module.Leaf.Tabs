<?php

use Rhubarb\Leaf\Tabs\Examples\SearchPanelTabsExample\Job;

include_once __DIR__.'/Job.php';
include_once __DIR__.'/../../../../module.leaf.searchpanel/src/Leaves/SearchPanelModel.php';
include_once __DIR__.'/../../../../module.leaf.searchpanel/src/Leaves/SearchPanelView.php';
include_once __DIR__.'/../../../../module.leaf.searchpanel/src/Leaves/SearchPanel.php';

function makeJob($title, $status, $sent)
{
    $job = new Job();
    $job->JobTitle = $title;
    $job->Status = $status;
    $job->Sent = $sent;
    $job->save();
}

// Make some fake jobs...
makeJob("Job A", "Incoming", true);
makeJob("Job B", "Incoming", false);
makeJob("Job C", "Outgoing", true);
makeJob("Job D", "Outgoing", false);
makeJob("Job E", "Stale", true);
makeJob("Job F", "Stale", false);