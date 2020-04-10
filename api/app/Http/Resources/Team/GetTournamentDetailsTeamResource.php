<?php

namespace App\Http\Resources\Team;

use App\Http\Resources\Tag\GetTournamentDetailsTagResource;
use App\Model\TagTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetTournamentDetailsTeamResource extends Resource
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
        // Nombre de joueurs atteints pour l'équipe
        $playersReached = $playersReached = TagTeam::where('team_id', $this->id)->count();

        // Joueurs faisant parti de l'équipe
        $players = DB::table('tag_team')
            ->join('tag', 'tag_team.tag_id', '=', 'tag.id')
            ->join('user', 'tag.user_id', '=', 'user.id')
            ->join('team', 'tag_team.team_id', '=', 'team.id')
            ->join('tournament', 'team.tournament_id', '=', 'tournament.id')
            ->leftJoin('reservation', function ($join) {
                $join->on('user.id', '=', 'reservation.user_id');
                $join->on('tournament.lan_id', '=', 'reservation.lan_id');
            })
            ->select(
                'tag_team.is_leader as is_leader',
                'tag.id as tag_id',
                'tag.name as tag_name',
                'user.first_name',
                'user.last_name',
                'reservation.id as reservation_id',
                'reservation.seat_id'
            )
            ->where('team.id', $this->id)
            ->get();

        return [
            'id' => intval($this->id),
            'name' => $this->name,
            'tag' => $this->tag,
            'players_reached' => intval($playersReached),
            'players' => GetTournamentDetailsTagResource::collection($players),
        ];
    }
}
