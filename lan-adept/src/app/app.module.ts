import { NgModule } from '@angular/core';
import { AngularFireModule } from '@angular/fire/compat';
import { AngularFireAuthModule } from '@angular/fire/compat/auth';
import { BrowserModule } from '@angular/platform-browser';

import { firebaseConfig } from '../environments/environment';
import { AppComponent } from './app.component';
import { AppRoutingModule } from './app-routing.module';
import { LoginComponent } from './pages/auth/login/login.component';
import { LogoutComponent } from './pages/auth/logout/logout.component';
import { CountdownComponent } from './pages/countdown/countdown/countdown.component';
import { DatecountdownComponent } from './pages/countdown/datecountdown/datecountdown.component';
import { HomeComponent } from './pages/home/home.component';
import { SeatsComponent } from './pages/seats/seats.component';

@NgModule({
  declarations: [
    AppComponent,
    CountdownComponent,
    DatecountdownComponent,
    HomeComponent,
    SeatsComponent,
    LoginComponent,
    LogoutComponent,
  ],
  imports: [
    AngularFireModule.initializeApp(firebaseConfig),
    AngularFireAuthModule, // auth
    BrowserModule,
    AppRoutingModule,
  ],
  bootstrap: [AppComponent],
})
export class AppModule {}
