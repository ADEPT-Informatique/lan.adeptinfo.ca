import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { map, Observable } from "rxjs";
import { Functions } from "src/app/shared";
import { environment } from "src/environments/environment";
import { Lan } from "../models/lan";

@Injectable({
  providedIn: "root",
})
export class LanService {
  BASE_URL = environment.apiUrl + "api/lan";

  constructor(private _httpClient: HttpClient) {}

  public getCurrentLan(): Observable<Lan> {
    return this._httpClient.get<Lan>(`${this.BASE_URL}/current`).pipe(
      map((lan) => {
        return Functions.buildLanFromRawResponse(lan);
      }),
    );
  }
}