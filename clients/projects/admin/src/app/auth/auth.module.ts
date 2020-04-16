import {NgModule} from '@angular/core';
import {SharedModule} from '../shared/shared.module';
import {AuthComponent} from './auth.component';
import {AuthRoutingModule} from './auth-routing.module';
import {NoAuthGuard} from './no-auth-guard.service';
import {AuthServiceConfig, FacebookLoginProvider, GoogleLoginProvider, SocialLoginModule} from 'angularx-social-login';
import {environment} from '../../environments/environment';

const config = new AuthServiceConfig([
  {
    id: GoogleLoginProvider.PROVIDER_ID,
    provider: new GoogleLoginProvider(environment.googleClientId)
  },
  {
    id: FacebookLoginProvider.PROVIDER_ID,
    provider: new FacebookLoginProvider(environment.facebookAppId)
  }
]);

export function provideConfig() {
  return config;
}

@NgModule({
  imports: [
    SharedModule,
    AuthRoutingModule,
    SocialLoginModule
  ],
  declarations: [
    AuthComponent
  ],
  providers: [
    NoAuthGuard,
    {
      provide: AuthServiceConfig,
      useFactory: provideConfig
    }
  ]
})
export class AuthModule {
}
