<?php

namespace App\Repositories;

use App\Model\Request;
use App\Model\Tag;
use App\Model\Team;
use Illuminate\Support\Collection;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux équipes.
 *
 * Interface TeamRepository
 */
interface TeamRepository
{
    /**
     * Créer une demande pour joindre une équipe.
     *
     * @param int $teamId    Id de l'équipe.
     * @param int $userTagId Id du tag du joueur qui souhaite joindre l'équipe.
     *
     * @return int Id de la requête créée.
     */
    public function createRequest(int $teamId, int $userTagId): int;

    /**
     * Créer une équipe.
     *
     * @param int    $tournamentId Id du tournoi de l'équipe.
     * @param string $name         Nom de l'équipe.
     * @param string $tag          Tag de l'équipe.
     *
     * @return int Id de l'équipe.
     */
    public function create(
        int $tournamentId,
        string $name,
        string $tag
    ): int;

    /**
     * Supprimer une requête.
     *
     * @param int $requestId Id de la requête.
     */
    public function deleteRequest(int $requestId): void;

    /**
     * Supprimer le lien entre un tag de joueur et une équipe.
     *
     * @param int $tagId  Id du tag du joueur.
     * @param int $teamId Id du tag de l'équipe.
     */
    public function deleteTagTeam(int $tagId, int $teamId): void;

    /**
     * Supprimer une équipe.
     *
     * @param int $teamId Id de l'équipe.
     */
    public function delete(int $teamId): void;

    /**
     * Trouver une équipe.
     *
     * @param int $id Id de l'équipe.
     *
     * @return Team|null Équipe trouvée, null si rien n'a été trouvé.
     */
    public function findById(int $id): ?Team;

    /**
     * Trouver une requête.
     *
     * @param int $id Id de la requête.
     *
     * @return Request|null Requête trouvée, null si rien n'a été trouvé.
     */
    public function findRequestById(int $id): ?Request;

    /**
     * Trouver un tag de joueur.
     *
     * @param int $id Id du tag.
     *
     * @return Tag|null Tag trouvé, null si rien n'a été trouvé.
     */
    public function findTagById(int $id): ?Tag;

    /**
     * Obtenir le nombre de requêtes pour entrer dans une équipe pour le chef d'une équipe.
     *
     * @param int $userId Id d'utilisateur du chef de l'équipe.
     * @param int $lanId  Id du LAN duquel l'utilisateur souhaite obtenir le nombre de requêtes.
     *
     * @return int Nombre de requêtes.
     */
    public function getLeadersRequestTotalCount(int $userId, int $lanId): int;

    /**
     * Obtenir les requêtes qu'a fait un utilisateur, avec quel tag de joueur, quelle équipe, et quel tournoi.
     *
     * @param int $userId Id de l'utilisateur.
     * @param int $lanId  Id du LAN dans duquel l'utilisateur souhaite obtenir ses requêtes.
     *
     * @return Collection Requêtes trouvées.
     */
    public function getRequestsForUser(int $userId, int $lanId): Collection;

    /**
     * Obtenir les requêtes pour entrer dans une équipe, avec le tag et le nom du joueur.
     *
     * @param int $teamId Id de l'équipe.
     *
     * @return Collection Requêtes trouvées.
     */
    public function getRequests(int $teamId): Collection;

    /**
     * Obtenir le tag étant arrivé le dernier, après le chef de l'équipe.
     *
     * @param int $teamId Id de l'équipe.
     *
     * @return Tag|null Tag trouvé, null si rien n'a été trouvé.
     */
    public function getTagWithMostSeniorityNotLeader(int $teamId): ?Tag;

    /**
     * Obtenir l'id du LAN d'une équipe.
     *
     * @param int $teamId Id de l'équipe.
     *
     * @return int|null Id du LAN trouvé, null si rien n'a été trouvé.
     */
    public function getTeamsLanId(int $teamId): ?int;

    /**
     * Obtenir les tags l'identité des joueurs d'une équipe.
     *
     * @param int $teamId Id de l'équipe.
     *
     * @return Collection Tag et identité des tags trouvés.
     */
    public function getUsersTeamTags(int $teamId): Collection;

    /**
     * Obtenir les équipes d'un utilisateur.
     *
     * @param int $userId Id de l'utilisateur.
     * @param int $lanId  Id du LAN dans lequel l'utilisateur cherche ses équipes.
     *
     * @return Collection Équipes trouvées.
     */
    public function getUserTeams(int $userId, int $lanId): Collection;

    /**
     * Lier un tag et une équipe.
     *
     * @param int  $tagId    Id du tag du joueur.
     * @param int  $teamId   Id de l'équipe.
     * @param bool $isLeader Si le tag du joueur est chef de l'équipe.
     */
    public function linkTagTeam(int $tagId, int $teamId, bool $isLeader): void;

    /**
     * Supprimer un utilisateur d'une équipe.
     *
     * @param int $userId Id de l'utilisateur.
     * @param int $teamId Id de l'équipe.
     */
    public function removeUserFromTeam(int $userId, int $teamId): void;

    /**
     * Changer de chef d'équipe.
     *
     * @param int $tagId  Id du tag du joueur qui sera le nouveau chef.
     * @param int $teamId Id de l'équipe.
     */
    public function switchLeader(int $tagId, int $teamId): void;

    /**
     * Déterminer si un utilisateur est le chef d'une équipe.
     *
     * @param int $teamId Id de l'équipe.
     * @param int $userId Id de l'utilisateur.
     *
     * @return bool Si l'utilisateur est le chef de l'équipe.
     */
    public function userIsLeader(int $teamId, int $userId): bool;
}
