import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { CountdownComponent } from './countdown/countdown/countdown.component';
import { DatecountdownComponent } from './countdown/datecountdown/datecountdown.component';
import { HomeComponent } from './home/home.component';
import { SeatsComponent } from './seats/seats.component';

@NgModule({
  declarations: [
    AppComponent,
    CountdownComponent,
    DatecountdownComponent,
    HomeComponent,
    SeatsComponent,

  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
