import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { SeatsComponent } from './seats/seats.component';

const routes: Routes = [
  { path: 'Home', component: HomeComponent },
  { path: 'places', component: SeatsComponent },
  { path: '**', redirectTo: 'Home' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule {}
