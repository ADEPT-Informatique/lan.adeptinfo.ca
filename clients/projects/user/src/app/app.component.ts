import { Component } from '@angular/core';
import { fromEvent, Observable, Subscription } from "rxjs";
import { UserService, AuthGuard, User, JwtService } from 'projects/core/src/public_api';
import { take } from 'rxjs/operators';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    resizeObservable$: Observable<Event>
    resizeSubscription$: Subscription



    constructor(public userService:UserService, public jwtService: JwtService) {
  
    }

    ngOnInit(): void {
        




    }


}
