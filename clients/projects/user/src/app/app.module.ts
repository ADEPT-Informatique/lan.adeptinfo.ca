import { BrowserModule } from "@angular/platform-browser";
import { NgModule } from "@angular/core";

import { AppComponent } from "./app.component";
import { RouterModule, Routes } from "@angular/router";
import { HttpClientModule } from "@angular/common/http";
import { HomeComponent } from "./home/home.component";
import { CountdownComponent } from "./countdown/countdown/countdown.component";
import { DatecountdownComponent } from "./countdown/datecountdown/datecountdown.component";
import { FormsModule } from "@angular/forms";
import { AuthModule } from "./auth/auth.module";
import { NotfoundComponent } from "./notfound/notfound.component";
import {
  UserService,
  JwtService,
  CoreModule,
  TournamentService
} from "projects/core/src/public_api";
import { ApiService } from "projects/core/src/lib/services/api.service";
import { PlacesComponent } from "./places/places.component";
import { SeatsioAngularModule } from "@seatsio/seatsio-angular";
import { ToastrModule } from "ngx-toastr";

const routes: Routes = [
  { path: "Home", component: HomeComponent },
  { path: "places", component: PlacesComponent },
  {
    path: "auth",
    loadChildren: () => import("./auth/auth.module").then(m => m.AuthModule)
  },
  {
    path: "tournaments",
    loadChildren: () =>
      import("./tournaments/tournaments.module").then(m => m.TournamentsModule)
  },
  { path: "**", redirectTo: "/Home" }
];

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    CountdownComponent,
    DatecountdownComponent,
    NotfoundComponent,
    PlacesComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    CoreModule,
    SeatsioAngularModule,
    RouterModule.forRoot(routes),
    AuthModule, // required animations module
    ToastrModule.forRoot() // ToastrModule added
  ],
  providers: [UserService, ApiService, JwtService, TournamentService],
  bootstrap: [AppComponent]
})
export class AppModule {}
