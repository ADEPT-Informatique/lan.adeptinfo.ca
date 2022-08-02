import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { CountdownComponent } from './countdown/countdown/countdown.component';
import { DatecountdownComponent } from './countdown/datecountdown/datecountdown.component';
import { HomeComponent } from './pages/home/home.component';
import { SeatsComponent } from './seats/seats.component';
import { SeatsioAngularModule } from '@seatsio/seatsio-angular';

@NgModule({
  declarations: [
    AppComponent,
    CountdownComponent,
    DatecountdownComponent,
    HomeComponent,
    SeatsComponent,

  ],
  imports: [
    SeatsioAngularModule,
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
