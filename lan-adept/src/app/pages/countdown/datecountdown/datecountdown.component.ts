import { AfterViewInit, Component } from '@angular/core';
import { interval } from 'rxjs';

@Component({
  selector: 'app-datecountdown',
  templateUrl: './datecountdown.component.html',
  styleUrls: ['./datecountdown.component.scss'],
})
export class DatecountdownComponent implements AfterViewInit {
  date: any = new Date('Mar 20, 2023 12:00:00');
  days: number = this.getDays();
  hours: number = this.getHours();
  minutes: number = this.getMinutes();
  seconds: number = this.getSeconds();

  ngAfterViewInit(): void {
    const ticker = interval(1000);
    ticker.subscribe((_) => {
      this.days = this.getDays();
      this.hours = this.getHours();
      this.minutes = this.getMinutes();
      this.seconds = this.getSeconds();
    });
  }

  getDate() {
    return this.date.toLocaleDateString('fr-fr', { hour: '2-digit', minute: '2-digit' });
  }
  getDays() {
    const now = new Date().getTime();
    const distance = this.date - now;
    return Math.floor((distance % (1000 * 60 * 60 * 24 * 365.25)) / (1000 * 60 * 60 * 24));
  }
  getHours() {
    const now = new Date().getTime();
    const distance = this.date - now;
    return Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  }
  getMinutes() {
    const now = new Date().getTime();
    const distance = this.date - now;
    return Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  }
  getSeconds() {
    const now = new Date().getTime();
    const distance = this.date - now;
    return Math.floor((distance % (1000 * 60)) / 1000);
  }
}
