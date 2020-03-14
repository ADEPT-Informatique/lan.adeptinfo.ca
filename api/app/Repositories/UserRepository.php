<?php

namespace App\Repositories;

use App\Model\Tag;
use App\Model\User;
use Illuminate\Pagination\AbstractPaginator;
use Laravel\Passport\Token;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux utilisateurs.
 *
 * Interface UserRepository
 */
interface UserRepository
{
    /**
     * Ajouter un code de confirmation à un utilisateur.
     *
     * @param string $email            Courriel de l'utilisateur à qui le code de confirmation est ajouté.
     * @param string $confirmationCode Code de confirmation.
     */
    public function addConfirmationCode(string $email, string $confirmationCode): void;

    /**
     * Ajouter Facebook à un utilisateur.
     *
     * @param string $email      Courriel de l'utilisateur.
     * @param string $facebookId Id Facebook de l'utilisateur.
     */
    public function addFacebookToUser(string $email, string $facebookId): void;

    /**
     * Ajouter Google à un utilisateur.
     *
     * @param string $email    Courriel de l'utilisateur.
     * @param string $googleId Id Google de l'utilisateur.
     */
    public function addGoogleToUser(string $email, string $googleId): void;

    /**
     * Confirmer un compte utilisateur.
     *
     * @param string $userId Id de l'utilisateur.
     */
    public function confirmAccount(string $userId): void;

    /**
     * Créer un utilisateur Facebook.
     *
     * @param string $facebookId Id facebook de l'utilisateur.
     * @param string $firstName  Prénom de l'utilisateur.
     * @param string $lastName   Nom de l'utilisateur.
     * @param string $email      Courriel de l'utilisateur.
     *
     * @return int Id de l'utilisateur créé.
     */
    public function createFacebookUser(string $facebookId, string $firstName, string $lastName, string $email): int;

    /**
     * Créer un utilisateur Google.
     *
     * @param string $googleId  Id Google de l'utilisateur.
     * @param string $firstName Prénom de l'utilisateur.
     * @param string $lastName  Nom de l'utilisateur.
     * @param string $email     Courriel de l'utilisateur.
     *
     * @return int Id de l'utilisateur créé.
     */
    public function createGoogleUser(string $googleId, string $firstName, string $lastName, string $email): int;

    /**
     * Créer un tag de joueur.
     *
     * @param int    $userId Id de l'utilisateur (joueur).
     * @param string $name   Nom du tag.
     *
     * @return int Id du Tag créé.
     */
    public function createTag(
        int $userId,
        string $name
    ): int;

    /**
     * Créer un nouvel utilisateur de l'application.
     *
     * @param string $firstName        Prénom de l'utilisateur.
     * @param string $lastName         Nom de l'utilisateur.
     * @param string $email            Courriel de l'utilisateur.
     * @param string $password         Mot de passe de l'utilisateur.
     * @param string $confirmationCode Code de confirmation.
     *
     * @return int Id de l'utilisateur créé.
     */
    public function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $confirmationCode
    ): int;

    /**
     * Supprimer un utilisateur.
     *
     * @param int $userId Id de l'utilisateur à supprimer.
     */
    public function deleteUserById(int $userId): void;

    /**
     * Trouver un utilisateur par son code de confirmation.
     *
     * @param string $confirmationCode Code de confirmation.
     *
     * @return User Utilisateur trouvé.
     */
    public function findByConfirmationCode(string $confirmationCode): User;

    /**
     * Trouver un utilisateur par son courriel.
     *
     * @param string $userEmail Courriel de l'utilisateur.
     *
     * @return User|null Utilisateur trouvé, null si rien n'a été trouvé.
     */
    public function findByEmail(string $userEmail): ?User;

    /**
     * Trouver un utilisateur.
     *
     * @param int $userId Id de l'utilisateur.
     *
     * @return User|null Utilisateur trouvé, null si rien n'a été trouvé.
     */
    public function findById(int $userId): ?User;

    /**
     * Trouver un tag de joueur.
     *
     * @param int $id Id du tag de joueur.
     *
     * @return Tag|null Tag trouvé, null si rien n'a été trouvé.
     */
    public function findTagById(int $id): ?Tag;

    /**
     * Obtenir les utilisateurs selon certains critères.
     *
     * @param string $queryString    Terme à rechercher.
     * @param string $orderColumn    Colonne à utiliser pour l'ordre des résultats.
     * @param string $orderDirection Si les résultats sont en ordre ascendant ou descendants.
     * @param int    $itemsPerPage   Nombre de résulats par page.
     * @param int    $currentPage    Page courante des résultats.
     *
     * @return AbstractPaginator Utilisateurs trouvés, paginés.
     */
    public function getPaginatedUsersCriteria(
        string $queryString,
        string $orderColumn,
        string $orderDirection,
        int $itemsPerPage,
        int $currentPage
    ): AbstractPaginator;

    /**
     * Révoquer un jeton d'actualisation.
     *
     * @param Token $token Jeton d'actualisation.
     */
    public function revokeRefreshToken(Token $token): void;
}
