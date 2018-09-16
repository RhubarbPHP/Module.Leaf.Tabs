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
            new TabDefinition('Incoming', ['Status' => 'Incoming']),        
            new TabDefinition('Outgoing', ['Status' => 'Outgoing']),        
            new TabDefinition('Stale', ['Status' => 'Stale'])        
        ]);
    }
}
```

This example creates a tab set with 3 tabs, 'Incoming', 'Outgoing' and 'Stale'. Each tab
is expressed using a `TabDefinition` object and the base implementation allows for the
tab name and an array of 'data' to associate with the tab.

By default the initially selected tab will be the first in the array.

## Using tabs to control content

When the user changes the selected tab the Tabs component will raise a `selectedTabChangedEvent`.
You can handle this event and then update your model and cause other elements on the page to
change. A common use case is to change the filters on a collection passed to a Table component.

```php
class MyPageView extends View
{
    protected function createSubLeaves()
    {
        $this->registerSubLeaf(
            $tabs = new Tabs(),
            
            // Set up a table with a collection matching the filter of the first tab.
            $table = new Table(Jobs::find(new Equals('Status', 'Incoming'))
            );
            
        $tabs->setTabDefinitions([
            new TabDefinition('Incoming', ['Status' => 'Incoming']),        
            new TabDefinition('Outgoing', ['Status' => 'Outgoing']),        
            new TabDefinition('Stale', ['Status' => 'Stale'])        
        ]);
        
        $tabs->selectedTabChangedEvent->attachHandler(function(TabDefinition $selectedTab) use ($table){
            $status = $selectedTab->data['Status'];
            
            // Change the table's collection and re-render it
            $table->getCollection()->replaceFilter(new Equals('Status', $status));
            $table->reRender();
        });
    }
}
```

In a real application the View would of course raise an event to let the Leaf return a new collection
to be given to the View, but hopefully you get the picture.

Notice the TabDefinition object is passed as an argument to the selectedTabChangedEvent. `$label`, `$data`
and `$selected` are all public properties you can access.

## Displaying counts

Most tab interfaces display a count on the tab to indicate the number of records found within. To do this
you need to firstly set the `$includeCountIfSupported` public property to true. You also need to 
attach a handler to the `getCollectionEvent`. In order to calculate the count your host needs to
be able to provide a stem Collection for the datasource that will drive the UI behind the tab.
This is simply counted and added to the label in the tab surrounded by brackets.

```php
class MyPageView extends View
{
    protected function createSubLeaves()
    {
        $this->registerSubLeaf(
            $tabs = new Tabs(),
            
            // Set up a table with a collection matching the filter of the first tab.
            $table = new Table(Jobs::find(new Equals('Status', 'Incoming'))
            );
            
        $tabs->setTabDefinitions([
            new TabDefinition('Incoming', ['Status' => 'Incoming']),        
            new TabDefinition('Outgoing', ['Status' => 'Outgoing']),        
            new TabDefinition('Stale', ['Status' => 'Stale'])        
        ]);
        
        $tabs->includeCountIfSupported = true;
        
        $tabs->getCollectionEvent->attachHandler(function(TabDefinition $tabToCount){
            return Jobs::find(new Equals('Status', $tabToCount->data['Status'));
        });
    }
}
```

## Sub classing TabDefinition

If you are creating a Tabs experience with more than one 'type' of tab it makes sense to create your
own extensions of TabDefinition and then use `instanceof` in your handler to understand how best
to respond to the change of tab.

In addition a sub class can customise the HTML for the tab itself by overriding the `getLabel($count)`
function.