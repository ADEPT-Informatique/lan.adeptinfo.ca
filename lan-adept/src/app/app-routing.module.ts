import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home.component';
import { SeatsComponent } from './pages/seats/seats.component';
import { LoginComponent } from './pages/auth/login/login.component';
import { LogoutComponent } from './pages/auth/logout/logout.component';

const routes: Routes = [
  { path: 'Home', component: HomeComponent },
  { path: 'places', component: SeatsComponent },
  {path:"login",component:LoginComponent},
  {path:"logout",component:LogoutComponent},
  { path: '**', redirectTo: 'Home' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
