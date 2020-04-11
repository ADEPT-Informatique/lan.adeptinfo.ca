<?php

namespace App\Http\Resources\Tournament;

use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
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
            // Obtenir le nombre de joueurs atteints
            $playersReached = TagTeam::where('team_id', $team->id)->count();

            // Si le nombre de joueur atteints est plus grand ou égal au nombre de joueurs à atteindre dans le tournoi
            if ($playersReached >= $this->players_to_reach) {
                // Augmenter le compteur d'équipes complète
                $teamsReached++;
                break;
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament_start)),
            'tournament_end' => date('Y-m-d H:i:s', strtotime($this->tournament_end)),
            'state' => $this->getCurrentState(),
            'teams_reached' => intval($teamsReached),
            'teams_to_reach' => intval($this->teams_to_reach),
        ];
    }
}
