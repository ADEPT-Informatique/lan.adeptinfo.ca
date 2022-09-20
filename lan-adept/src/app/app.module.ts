import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { CountdownComponent } from './pages/countdown/countdown/countdown.component';
import { DatecountdownComponent } from './pages/countdown/datecountdown/datecountdown.component';
import { HomeComponent } from './pages/home/home.component';
import { SeatsComponent } from './pages/seats/seats.component';
import { LoginComponent } from './pages/auth/login/login.component';
import { LogoutComponent } from './pages/auth/logout/logout.component';
import { environment } from '../environments/environment';
import { AngularFireModule } from '@angular/fire/compat';
import { AngularFireAuthModule } from '@angular/fire/compat/auth';
import { CoreModule } from './core/core.module';
import { ProfileComponent } from './pages/auth/profile/profile.component';
import { UserService } from './core/services/user.service';
import { AuthService } from './core/services/auth.service';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { AuthInterceptor } from './core/interceptors/auth.interceptor';

@NgModule({
  declarations: [
    AppComponent,
    CountdownComponent,
    DatecountdownComponent,
    HomeComponent,
    SeatsComponent,
    LoginComponent,
    LogoutComponent,
    ProfileComponent,
  ],
  imports: [
    AngularFireModule.initializeApp(environment.firebase),
    AngularFireAuthModule, // auth
    BrowserModule,
    HttpClientModule,
    AppRoutingModule,
    CoreModule,
  ],
  providers: [UserService, AuthService, { provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true }],
  bootstrap: [AppComponent],
})
export class AppModule {}
