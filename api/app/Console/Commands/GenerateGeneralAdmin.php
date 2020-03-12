<?php

namespace App\Console\Commands;

use App\Model\GlobalRole;
use App\Model\User;
use App\Rules\User\UniqueEmailSocialLogin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Générer un administrateur général, possédant toutes les permissions dans l'application.
 *
 * Class GenerateGeneralAdmin
 */
class GenerateGeneralAdmin extends Command
{
    /**
     * Nom et signature de la commande.
     *
     * @var string
     */
    protected $signature = 'lan:general-admin {--default}';

    /**
     * Description de la commande.
     *
     * @var string
     */
    protected $description = 'Générer un administrateur général. Cet utilisateur est requis pour promouvoir tout autre utilisateur au rang d\'administrateur, et pour créer des LANs.';

    public function handle()
    {
        $this->preconditions();

        $email = null;
        $firstName = null;
        $lastName = null;
        $password = null;

        $emailValidator = null;
        $firstNameValidator = null;
        $lastNameValidator = null;
        $passwordValidator = null;

        if ($this->option('default') == 1) {

            // Courriel
            do {
                $email = $this->ask('Courriel (Format de courriel valide, s\'il n\'est pas en attente de confirmation, il est nouveau dans l\'application ou est utilisé par Google ou Facebook.  : ');
                $emailValidator = Validator::make(['email' => $email], ['email' => ['required', 'email', new UniqueEmailSocialLogin()]]);

                if ($emailValidator->fails()) {
                    $this->warn('Courriel invalide. Veuillez réessayer.');
                }
            } while ($emailValidator->fails());

            // Prénom
            do {
                $firstName = $this->ask('Prénom (255 caractères max) : ');
                $firstNameValidator = Validator::make(['first_name' => $firstName], ['first_name' => 'required|max:255']);
                if ($firstNameValidator->fails()) {
                    $this->warn('Prénom invalide. Veuillez réessayer.');
                }
            } while ($firstNameValidator->fails());

            // Nom
            do {
                $lastName = $this->ask('Nom (255 caractères max) : ');
                $lastNameValidator = Validator::make(['last_name' => $lastName], ['last_name' => 'required|max:255']);

                if ($lastNameValidator->fails()) {
                    $this->warn('Nom invalide. Veuillez réessayer.');
                }
            } while ($lastNameValidator->fails());

            // Mot de passe
            do {
                $password = $this->secret('Mot de passe invalide (6 caractères min, 20 caractères max) : ');
                $passwordConfirmation = $this->secret('Confirm password');
                $samePasswords = $password == $passwordConfirmation;
                $passwordValidator = Validator::make(['password' => $password], ['password' => 'required|min:6|max:20']);

                if ($passwordValidator->fails() || !$samePasswords) {
                    $this->warn('Mot de passe invalide. Veuillez réessayer.');
                }
            } while ($passwordValidator->fails() || !$samePasswords);

        } else {
            $firstName = 'admin';
            $lastName = 'admin';
            $email = 'admin@adeptinfo.ca';
            $password = 'Passw0rd!';
        }

        // Ajouter l'utilisateur
        DB::table('user')->insertOrIgnore([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
            'is_confirmed' => true,
        ]);

        // Lier l'utilisateur créé au rôle d'administrateur général (Possède toutes les permissions)
        DB::table('global_role_user')->insertOrIgnore([
            'role_id' => GlobalRole::where('name', 'general-admin')->first()->id,
            'user_id' => User::where('email', $email)->first()->id
        ]);

        // Afficher l'utilisateur créé dans la console
        $this->info('Administrateur général généré.');
        $headers = ['email', 'first name', 'last name'];
        $user = json_decode(json_encode(DB::table('user')->where('email', $email)->get(['email', 'first_name', 'last_name'])), true);
        $this->table($headers, $user);
        $this->line('');
    }

    /**
     * Précondition pour pouvoir utiliser la commande :
     * Le rôle global general-admin doit exister.
     */
    private function preconditions(): void
    {
        if (GlobalRole::where('name', 'general-admin')->count() < 1) {
            $this->error('Précondition non remplie. Indice: Essayez d\'exécuter la commande "lan:roles".');
            exit();
        }
    }
}
