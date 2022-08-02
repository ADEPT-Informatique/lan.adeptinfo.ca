// declare const $: any;
import { Component, OnInit } from '@angular/core';
import { interval } from 'rxjs';
import { fromEvent, Observable, Subscription } from "rxjs";
const ticker = interval(1000);

@Component({
	selector: 'app-home',
	templateUrl: './home.component.html',
	styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
	isconnected: boolean = false;
	constructor() {
	}

	ngOnInit(): void {
	}

	ngAfterViewInit(): void {

	}
}
