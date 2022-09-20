import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { environment } from "src/environments/environment";
import { User } from "../models/user";
import { AuthService } from "./auth.service";

@Injectable({
  providedIn: "root",
})
export class UserService {
  constructor(private _authService: AuthService, private _httpClient: HttpClient) {
    console.log(this._authService.currentUser);
  }

  public isConnected(): boolean {
    return this._authService.isLoggedIn;
  }

  public me(): Observable<User> {
    return this._httpClient.get<User>(environment.apiUrl + "api/users/me");
  }
}
