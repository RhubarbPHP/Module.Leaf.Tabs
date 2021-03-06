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

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;
use Rhubarb\Leaf\Leaves\UrlStateView;
use Rhubarb\Leaf\Views\View;

class TabsView extends UrlStateView
{
    /**
     * @var TabsModel
     */
    protected $model;

    protected function getViewBridgeName()
    {
        return "TabsViewBridge";
    }

    public function getDeploymentPackage()
    {
        $package = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__."/TabsViewBridge.js";

        return $package;
    }

    protected function printTab($tab)
    {
        $selected = ($tab->selected) ? " class=\"selected\"" : "";
        print "<li{$selected}><a href='#'>" . $tab->getLabel($this->model->getCountForTabEvent->raise($tab)) . "</a></li>";
    }

    public function printViewContent()
    {
        print "<ul class='tabs'>";

        foreach ($this->model->tabs as $tab) {
            $this->printTab($tab);
        }

        print "</ul>";
    }

    public function parseUrlState(WebRequest $request)
    {
        if ($tab = $request->get($this->model->urlStateName)){
            $this->model->tabSelectedEvent->raise($tab);
        }
    }
}