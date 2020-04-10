<?php

namespace App\Http\Resources\Team;

use App\Model\Request;
use App\Model\TagTeam;
use App\Model\Tournament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetUserTeamsResource extends Resource
{
    /**
     * Transformer la ressource en tableau.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        // Obtenir le nombre de joueur atteints
        $playersReached = TagTeam::where('team_id', $this->id)
            ->count();

        // Obtenir le tournoi
        $tournament = Tournament::find($this->tournament_id);

        // Obtenir les requêtes pour entrer dans l'équipe
        $requests = Request::where('team_id', $this->id)->count();

        // Obtenir l'id des tags de l'utilisateur
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', Auth::id())
            ->pluck('id')
            ->toArray();

        // Obtenir les liens entre les tags et l'équipe
        $tagTeam = TagTeam::whereIn('tag_id', $tagIds)
            ->where('team_id', $this->id)
            ->first();

        // Donner l'état du joueur dans l'équipe
        $playersState = null;
        if (!is_null($tagTeam)) {
            $playersState = $tagTeam->is_leader ? 'leader' : 'confirmed';
        } else {
            $playersState = 'not-confirmed';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'tag' => $this->tag,
            'players_reached' => $playersReached,
            'players_to_reach' => intval($tournament->players_to_reach),
            'tournament_name' => $tournament->name,
            'requests' => $requests,
            'player_state' => $playersState,
        ];
    }
}
