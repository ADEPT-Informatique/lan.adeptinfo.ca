<?php

/** @noinspection ALL */

/*
|--------------------------------------------------------------------------
| Routes de l'application
|--------------------------------------------------------------------------
|
| Toutes les points d'accès à l'API sont définis ici.
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['middleware' => ['language']], function ($api) {

        // Connection d'obention du token utilisateur
        $api->group(['namespace' => '\Laravel\Passport\Http\Controllers', 'middleware' => ['login']], function ($api) {
            $api->post('oauth/token', 'AccessTokenController@issueToken');
        });

        // Requêtes ne nécessitants pas d'être authentifié
        $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

            // Test
            $api->get('', 'TestController@base');

            // Utilisateur
            $api->post('user', 'UserController@signUp');
            $api->post('user/facebook', 'UserController@signInFacebook');
            $api->get('user/confirm/{confirmation_code}', 'UserController@confirm');
            $api->post('user/google', 'UserController@signInGoogle');

            // LAN
            $api->get('lan', 'LanController@get');
            $api->get('lan/all', 'LanController@getAll');

            // Contribution
            $api->get('contribution/category', 'ContributionController@getCategories');
            $api->get('contribution', 'ContributionController@getContributions');

            // Tournoi
            $api->get('tournament/details/{tournament_id}', 'TournamentController@get');
            $api->get('tournament/all', 'TournamentController@getAll');
        });

        // Requêtes nécessitants d'être authentifié
        $api->group(['middleware' => ['auth:api']], function ($api) {
            $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

                /*
                 * Joueur
                 */

                // Place
                $api->post('seat/book/{seat_id}', 'SeatController@book');
                $api->delete('seat/book/{seat_id}', 'SeatController@unBook');

                // Équipe
                $api->post('team', 'TeamController@create');
                $api->post('team/request', 'TeamController@createRequest');
                $api->get('team/request', 'TeamController@getRequests');
                $api->get('team/user', 'TeamController@getUserTeams');
                $api->get('team/details', 'TeamController@getUsersTeamDetails');
                $api->put('team/leader', 'TeamController@changeLeader');
                $api->post('team/accept', 'TeamController@acceptRequest');
                $api->post('team/leave', 'TeamController@leave');
                $api->delete('team/kick', 'TeamController@kick');
                $api->delete('team/leader', 'TeamController@deleteLeader');
                $api->delete('team/request/leader', 'TeamController@deleteRequestLeader');
                $api->delete('team/request/player', 'TeamController@deleteRequestPlayer');

                // Utilisateur
                $api->post('tag', 'UserController@createTag');
                $api->delete('user', 'UserController@deleteUser');
                $api->post('user/logout', 'UserController@logOut');
                $api->get('user', 'UserController@getUsers');
                $api->get('user/summary', 'UserController@getUserSummary');
                $api->post('user/details', 'UserController@getUserDetails');

                /*
                 * Administrateur
                 */

                // LAN
                $api->post('lan', 'LanController@create');
                $api->post('lan/current', 'LanController@setCurrent');
                $api->put('lan', 'LanController@update');
                $api->post('lan/image', 'LanController@addLanImage');
                $api->delete('lan/image', 'LanController@deleteLanImages');

                // Contribution
                $api->post('contribution/category', 'ContributionController@createCategory');
                $api->delete('contribution/category', 'ContributionController@deleteCategory');
                $api->post('contribution', 'ContributionController@createContribution');
                $api->delete('contribution', 'ContributionController@deleteContribution');

                // Place
                $api->post('seat/confirm/{seat_id}', 'SeatController@confirmArrival');
                $api->delete('seat/confirm/{seat_id}', 'SeatController@unConfirmArrival');
                $api->post('seat/assign/{seat_id}', 'SeatController@assign');
                $api->delete('seat/assign/{seat_id}', 'SeatController@unAssign');
                $api->get('seat/charts', 'SeatController@getSeatCharts');

                // Tournoi
                $api->post('tournament', 'TournamentController@create');
                $api->put('tournament/{tournament_id}', 'TournamentController@update');
                $api->delete('tournament/{tournament_id}', 'TournamentController@delete');
                $api->post('tournament/{tournament_id}/quit', 'TournamentController@quit');
                $api->get('tournament/all/organizer', 'TournamentController@getAllForOrganizer');
                $api->post('tournament/{tournament_id}/organizer', 'TournamentController@addOrganizer');
                $api->delete('tournament/{tournament_id}/organizer', 'TournamentController@removeOrganizer');
                $api->delete('team/admin', 'TeamController@deleteAdmin');

                // Rôle de LAN
                $api->post('role/lan', 'RoleController@createLanRole');
                $api->put('role/lan', 'RoleController@updateLanRole');
                $api->post('role/lan/assign', 'RoleController@assignLanRole');
                $api->post('role/lan/permissions', 'RoleController@addPermissionsLanRole');
                $api->delete('role/lan/permissions', 'RoleController@deletePermissionsLanRole');
                $api->delete('role/lan', 'RoleController@deleteLanRole');
                $api->get('role/lan', 'RoleController@getLanRoles');
                $api->get('role/lan/permissions', 'RoleController@getLanRolePermissions');
                $api->get('role/lan/users', 'RoleController@getLanRoleUsers');

                // Rôle global
                $api->post('role/global', 'RoleController@createGlobalRole');
                $api->put('role/global', 'RoleController@updateGlobalRole');
                $api->post('role/global/assign', 'RoleController@assignGlobalRole');
                $api->post('role/global/permissions', 'RoleController@addPermissionsGlobalRole');
                $api->delete('role/global/permissions', 'RoleController@deletePermissionsGlobalRole');
                $api->delete('role/global', 'RoleController@deleteGlobalRole');
                $api->get('role/global', 'RoleController@getGlobalRoles');
                $api->get('role/global/permissions', 'RoleController@getGlobalRolePermissions');
                $api->get('role/global/users', 'RoleController@getGlobalRoleUsers');

                // Permissions
                $api->get('role/permissions', 'RoleController@getPermissions');

                // Général
                $api->get('admin/roles', 'UserController@getAdminRoles');
                $api->get('admin/summary', 'UserController@getAdminSummary');
            });
        });
    });
});
