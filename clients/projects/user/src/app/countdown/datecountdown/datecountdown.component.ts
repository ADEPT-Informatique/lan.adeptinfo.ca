import { Component, OnInit } from '@angular/core';
import { interval } from 'rxjs';

@Component({
  selector: 'app-datecountdown',
  templateUrl: './datecountdown.component.html',
  styleUrls: ['./datecountdown.component.css']
})
export class DatecountdownComponent implements OnInit {
  date: any = new Date("Mar 20, 2020 12:00:00");
  days: number = this.getDays();
  hours: number = this.getHours();
  minutes: number = this.getMinutes();
  seconds: number = this.getSeconds();

  constructor() { }

  ngOnInit(): void {

  }

  ngAfterViewInit(): void {
    const ticker = interval(1000);
    ticker.subscribe(_ => {
      this.days = this.getDays();
      this.hours = this.getHours();
      this.minutes = this.getMinutes();
      this.seconds = this.getSeconds();
    });
  }

  getDate() {
    return this.date.toLocaleDateString("fr-fr", { hour: "2-digit", minute: "2-digit" })
  }
  getDays() {
    let now = new Date().getTime();
    let distance = this.date - now;
    return Math.floor(distance % (1000 * 60 * 60 * 24 * 365.25) / (1000 * 60 * 60 * 24));
  }
  getHours() {
    let now = new Date().getTime();
    let distance = this.date - now;
    return Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  }
  getMinutes() {
    let now = new Date().getTime();
    let distance = this.date - now;
    return Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  }
  getSeconds() {
    let now = new Date().getTime();
    let distance = this.date - now;
    return Math.floor((distance % (1000 * 60)) / 1000);
  }
}
