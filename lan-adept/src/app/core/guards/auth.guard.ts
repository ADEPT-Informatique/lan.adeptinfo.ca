import { Injectable } from "@angular/core";
import { CanActivate } from "@angular/router";
import { filter, firstValueFrom, from, Observable, of, switchMap } from "rxjs";
import { AuthService } from "../services/auth.service";

@Injectable({
  providedIn: "root",
})
export class AuthGuard implements CanActivate {
  constructor(private _authService: AuthService) {}
  async canActivate(): Promise<boolean> {
    return firstValueFrom(
      this._authService.afAuth.authState.pipe(
        filter((x) => !!x),
        switchMap((x) => (x ? from(x.getIdToken().then((x) => !!x)) : of(false))),
      ),
    );
  }
}
