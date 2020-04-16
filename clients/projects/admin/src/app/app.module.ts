import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';

import {AppComponent} from './app.component';
import {CoreModule} from 'core';
import {FooterComponent} from './shared/layout/footer.component';
import {SharedModule} from './shared/shared.module';
import {AppRoutingModule} from './app-routing.module';
import {AuthModule} from './auth/auth.module';
import {LandingModule} from './landing/landing.module';

@NgModule({
  declarations: [
    AppComponent,
    FooterComponent
  ],
  imports: [
    BrowserModule,
    CoreModule,
    SharedModule,
    AuthModule,
    LandingModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {
}
