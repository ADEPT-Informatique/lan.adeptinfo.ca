// declare const $: any;
import * as $ from 'jquery';
import { Component, OnInit } from '@angular/core';
import { interval } from 'rxjs';
import { fromEvent, Observable, Subscription } from "rxjs";
import { UserService } from 'projects/core/src/public_api';
const ticker = interval(1000);

@Component({
	selector: 'app-home',
	templateUrl: './home.component.html',
	styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
	isconnected: boolean = false;
	constructor(public userService: UserService) {
		this.isconnected = this.userService.isConnected();
	}

	ngOnInit(): void {
	}

	ngAfterViewInit(): void {

	}
}
