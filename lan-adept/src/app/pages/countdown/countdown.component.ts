import { Component } from "@angular/core";
import { interval } from "rxjs";
import { Lan } from "src/app/core/models/lan";
import { LanService } from "src/app/core/services/lan.service";

@Component({
  selector: "app-countdown",
  templateUrl: "./countdown.component.html",
  styleUrls: ["./countdown.component.scss"],
})
export class CountdownComponent {
  currentLan: Lan | undefined;

  days: number = this.getDays();
  hours: number = this.getHours();
  minutes: number = this.getMinutes();
  seconds: number = this.getSeconds();

  constructor(private lanService: LanService) {
    this.lanService.getCurrentLan().subscribe((lan: Lan) => {
      this.currentLan = lan;
    });

    const ticker = interval(1000);
    ticker.subscribe(() => {
      this.days = this.getDays();
      this.hours = this.getHours();
      this.minutes = this.getMinutes();
      this.seconds = this.getSeconds();
    });
  }

  getCurrentDate(): Date | null {
    if (this.currentLan !== undefined) {
      const startingDate = this.currentLan.date;

      if (startingDate > new Date()) {
        return startingDate;
      }
    }

    return null;
  }

  getDate() {
    const currentDate = this.getCurrentDate();

    if (currentDate == null) {
      return "Le LAN est terminé! Rendez-vous au prochain.";
    }

    return currentDate.toLocaleDateString("fr-fr", { hour: "2-digit", minute: "2-digit" });
  }

  getDays() {
    const currentDate = this.getCurrentDate();

    if (currentDate == null) {
      return 0;
    }

    let now = new Date().getTime();
    let distance = currentDate.getTime() - now;

    return Math.floor((distance % (1000 * 60 * 60 * 24 * 365.25)) / (1000 * 60 * 60 * 24));
  }
  getHours() {
    const currentDate = this.getCurrentDate();

    if (currentDate == null) {
      return 0;
    }

    let now = new Date().getTime();
    let distance = currentDate.getTime() - now;
    return Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  }
  getMinutes() {
    const currentDate = this.getCurrentDate();

    if (currentDate == null) {
      return 0;
    }

    let now = new Date().getTime();
    let distance = currentDate.getTime() - now;
    return Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  }
  getSeconds() {
    const currentDate = this.getCurrentDate();

    if (currentDate == null) {
      return 0;
    }

    let now = new Date().getTime();
    let distance = currentDate.getTime() - now;
    return Math.floor((distance % (1000 * 60)) / 1000);
  }
}
