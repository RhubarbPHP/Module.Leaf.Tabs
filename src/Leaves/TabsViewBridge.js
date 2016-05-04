var tabsPresenter = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.ViewBridge.apply(this, arguments);
};

tabsPresenter.prototype = new window.rhubarb.viewBridgeClasses.ViewBridge();
tabsPresenter.prototype.constructor = tabsPresenter;

tabsPresenter.prototype.attachEvents = function () {
    var nodes = this.viewNode.querySelectorAll('li');
    var self = this;

    for(var i = 0; i<nodes.length; i++){
        nodes[i].addEventListener('click',function () {
            var lis = $(this).parent()[0].childNodes;
            var index = Array.prototype.indexOf.call(lis, this);

            self.raiseServerEvent("TabSelected", index);

            $('ul:first', self.element).children().removeClass('selected');
            $(this).addClass('selected');
        });
    }
};

window.rhubarb.viewBridgeClasses.TabsViewBridge = tabsPresenter;