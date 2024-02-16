function registerEvent(mobile, action) {
  if (mobile) {
    window.EBWidgets.createWidget({
      widgetType: "checkout",
      eventId: "428949488467",
      modal: true,
      modalTriggerElementId: "eventbrite-widget-modal-trigger-428949488467",
      onOrderComplete: action,
    });
  } else {
    window.EBWidgets.createWidget({
      // Required
      widgetType: "checkout",
      eventId: "428949488467",
      iframeContainerId: "eventbrite-widget-container-428949488467",

      // Optional
      iframeContainerHeight: 725, // Widget height in pixels. Defaults to a minimum of 425px if not provided
      onOrderComplete: action, // Method called when an order has successfully completed
    });
  }
}
