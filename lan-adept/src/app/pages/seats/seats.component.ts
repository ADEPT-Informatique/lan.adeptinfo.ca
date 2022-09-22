import { Component, CUSTOM_ELEMENTS_SCHEMA, OnInit } from '@angular/core';
import { UserService } from '../../core/services/user.service';


@Component({
  selector: 'app-seats',
  templateUrl: './seats.component.html',
  standalone: true,
  styleUrls: ['./seats.component.scss'],
  schemas:[CUSTOM_ELEMENTS_SCHEMA]
})
export class SeatsComponent {
  constructor(public userService: UserService) {}
}
