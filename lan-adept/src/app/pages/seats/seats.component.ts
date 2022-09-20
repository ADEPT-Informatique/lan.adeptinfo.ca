import { Component } from '@angular/core';
import { UserService } from '../../core/services/user.service';

@Component({
  selector: 'app-seats',
  templateUrl: './seats.component.html',
  styleUrls: ['./seats.component.scss'],
})
export class SeatsComponent {
  constructor(public userService: UserService) {
    // (window as any)?.PretixWidget?.open('https://pretix.eu/adept/test/');
  }
}
