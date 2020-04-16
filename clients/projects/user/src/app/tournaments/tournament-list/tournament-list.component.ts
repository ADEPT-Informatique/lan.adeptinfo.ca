import { Component, OnInit } from '@angular/core';
import { UserService, TournamentService } from 'projects/core/src/public_api';

@Component({
  selector: 'app-tournament-list',
  templateUrl: './tournament-list.component.html',
  styleUrls: ['./tournament-list.component.css']
})
export class TournamentListComponent implements OnInit {

  tournaments;

  constructor(private tournamentService: TournamentService) { }

  ngOnInit() {
    this.tournaments = this.tournamentService.allTournament().subscribe(
      resp => {
        console.log(resp[0].name)
        this.tournaments = resp;
      },
      error => {

      }
    )
    console.log("List loaded");
    
  }

  getTournaments(){
    var output = [];
    this.tournaments.forEach(element => {
      if (element.state != 'hidden'){
        output.push(element)
      }
    });
    return output;

  }

}
