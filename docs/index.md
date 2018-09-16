Tabs
====

The Tabs application component lets you display a selection of tabs and have events raised
when the user changes the selected tab.

The tab is changed by raising a server event then swapping CSS classes on the tabs to
achieve the effect. The tab control is not by default re-rendered, however it is quite
common that other components affected by the change of selected tab may re-render.

## Creating the tab control

In your hosting page's View:

```php
class MyPageView extends View
{
    protected function createSubLeaves()
    {
        $this->registerSubLeaf(
            $tabs = new Tabs()
            );
            
        $tabs->setTabDefinitions([
            new TabDefinition("Incoming", ["Status" => "Incoming"]),        
            new TabDefinition("Outgoing", ["Status" => "Outgoing"]),        
            new TabDefinition("Stale", ["Status" => "Stale"])        
        ]);
    }
}
```

This example creates a tab set with 3 tabs, 'Incoming', 'Outgoing' and 'Stale'. Each tab
is expressed using a `TabDefinition` object and the base implementation allows for the
tab name and an array of 'data' to associate with the tab.

By default the initially selected tab will be the first in the array.


