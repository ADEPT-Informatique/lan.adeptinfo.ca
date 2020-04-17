import { Component, OnInit } from '@angular/core';
import { UserService } from 'projects/core/src/public_api';
import { Route } from '@angular/compiler/src/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-logout',
  templateUrl: './logout.component.html',
  styleUrls: ['./logout.component.css']
})
export class LogoutComponent implements OnInit {

  constructor(private userService: UserService,private router: Router) {}

  ngOnInit() {
    this.userService.logout();
    this.router.navigate(['/home']);
  }

}
