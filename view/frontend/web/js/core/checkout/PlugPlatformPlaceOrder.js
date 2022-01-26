const PlugPlatformPlaceOrder = function (platformObject, data, event) {
    this.platformObject = platformObject;
    this.data = data;
    this.event = event;
}
PlugPlatformPlaceOrder.prototype.placeOrder = function() {
    return this.platformObject.placeOrder(
        this.data,
        this.event
    );
}
