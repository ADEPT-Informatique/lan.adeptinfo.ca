import { Injectable } from "@angular/core";
import { ApiService } from "./api.service";

@Injectable({
  providedIn: "root"
})
export class TournamentService {
  constructor(private apiService: ApiService) {}

  createTournament() {}

  deleteTournament() {}

  tournamentByOrganizer() {}

  allTournament() {
    return this.apiService.get("/tournament/all");
  }

  updateTournament() {}

  detailsTournament() {}

  addOrganizer() {}

  removeOrganiser() {}
}
