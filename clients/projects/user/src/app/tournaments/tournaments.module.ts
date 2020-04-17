import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TournamentListComponent } from './tournament-list/tournament-list.component';
import { TournamentInfoComponent } from './tournament-info/tournament-info.component';
import { Routes, RouterModule } from '@angular/router';
import {MatListModule} from '@angular/material/list'; 

const appRoutes: Routes = [
  { path: "list", component: TournamentListComponent },
  { path: "info", component: TournamentInfoComponent },
  { path: "", redirectTo: "list"  }
];

@NgModule({
  declarations: [TournamentListComponent, TournamentInfoComponent],
  imports: [
    CommonModule,
    MatListModule,
    RouterModule.forChild(appRoutes)
  ]
})
export class TournamentsModule { }
