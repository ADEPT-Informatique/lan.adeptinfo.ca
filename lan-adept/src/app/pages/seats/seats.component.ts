import { NgIf } from "@angular/common";
import { Component, CUSTOM_ELEMENTS_SCHEMA, OnInit, AfterViewInit, NgZone } from "@angular/core";
import { DeviceDetectorService } from "ngx-device-detector";
import { UserService } from "../../core/services/user.service";

declare function registerEvent(mobile: boolean, action: any): void;

@Component({
  selector: "app-seats",
  templateUrl: "./seats.component.html",
  standalone: true,
  styleUrls: ["./seats.component.scss"],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [NgIf],
})
export class SeatsComponent {
  constructor(private ngZone: NgZone, public deviceService: DeviceDetectorService) {
    const eventbriteCallback = function () {
      console.log("Order complete!");
    };
    this.ngZone.runOutsideAngular(() => {
      registerEvent(this.deviceService.isMobile(), eventbriteCallback);
    });
  }
}
