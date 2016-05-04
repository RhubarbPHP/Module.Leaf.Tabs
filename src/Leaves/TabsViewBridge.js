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
            var lis = this.parentNode.childNodes;
            var index = Array.prototype.indexOf.call(lis, this);

            self.raiseServerEvent("tabSelected", index);

            for(var j = 0; j<nodes.length; j++){
                nodes[j].classList.remove('selected');
            }

            this.classList.add('selected');
        });
    }
};

window.rhubarb.viewBridgeClasses.TabsViewBridge = tabsPresenter;