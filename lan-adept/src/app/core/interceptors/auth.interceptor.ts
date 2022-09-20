import { Injectable } from "@angular/core";
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from "@angular/common/http";
import { from, Observable, of, switchMap } from "rxjs";
import { AuthService } from "../services/auth.service";

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
  constructor(private _authService: AuthService) {}

  public intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return from(this._authService.afAuth.currentUser).pipe(
      switchMap((x) => (x ? from(x.getIdToken()) : of(null))),
      switchMap((token) => {
        //add the token to the header if it exists
        let newRequest;
        if (token) {
          newRequest = request.clone({
            setHeaders: {
              Authorization: `Bearer ${token}`,
            },
          });
        }

        return next.handle(newRequest || request);
      }),
    );
  }
}
