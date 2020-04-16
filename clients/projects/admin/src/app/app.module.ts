import { ApiService } from './../../../core/src/lib/services/api.service';
import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { core } from '@angular/compiler';
import { FooterComponent } from './shared/layout/footer.component';
import { SharedModule } from './shared/shared.module';
import { AppRoutingModule } from './app-routing.module';
import { AuthModule } from './auth/auth.module';
import { LandingModule } from './landing/landing.module';
import { UserService, LanService, CoreModule } from 'projects/core/src/public_api';

@NgModule({
  declarations: [
    AppComponent,
    FooterComponent
  ],
  imports: [
    BrowserModule,
    SharedModule,
    AuthModule,
    CoreModule,
    LandingModule,
    AppRoutingModule
  ],
  providers: [UserService, ApiService, LanService],
  bootstrap: [AppComponent]
})
export class AppModule {
}
