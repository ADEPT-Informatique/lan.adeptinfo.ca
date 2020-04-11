<?php

namespace App\Http\Resources\Tournament;

use App\Http\Resources\Team\GetTournamentDetailsTeamResource;
use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentDetailsResource extends JsonResource
{
    /**
     * Transformer la ressource en tableau.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        // Obtenir les équipes du tournoi
        $teams = Team::where('tournament_id', $this->id)->get();

        $teamsReached = 0;

        // Pour chaque équipe
        foreach ($teams as $team) {
            // Obtenir le nombre de joueurs atteints pour l'équipe
            $playersReached = TagTeam::where('team_id', $team->id)->count();

            // Si le nombre de joueur atteints est plus grand ou égal au nombre de joueurs à atteindre dans le tournoi
            if ($playersReached >= $this->players_to_reach) {
                // Augmenter le compteur d'équipes complète
                $teamsReached++;
                break;
            }
        }

        // Ajouter l'id du LAN aux équipes
        $teams->map(function ($team) {
            $team['lan_id'] = $this->lan_id;

            return $team;
        });

        return [
            'id' => intval($this->id),
            'name' => $this->name,
            'rules' => $this->rules,
            'price' => intval($this->price),
            'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament_start)),
            'tournament_end' => date('Y-m-d H:i:s', strtotime($this->tournament_end)),
            'teams_to_reach' => intval($this->teams_to_reach),
            'teams_reached' => $teamsReached,
            'players_to_reach' => intval($this->players_to_reach),
            'state' => $this->getCurrentState(),
            'teams' => GetTournamentDetailsTeamResource::collection($teams),
        ];
    }
}
