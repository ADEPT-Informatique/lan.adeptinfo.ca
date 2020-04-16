import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {ActivatedRoute, Router} from '@angular/router';
import {MediaMatcher} from '@angular/cdk/layout';
import {AuthService, FacebookLoginProvider, GoogleLoginProvider} from 'angularx-social-login';
import { UserService } from 'projects/core/src/public_api';

@Component({
  selector: 'app-auth-page',
  templateUrl: './auth.component.html',
  styleUrls: ['./auth.component.css']
})
/**
 * Authentification des utilisateurs.
 */
export class AuthComponent {

  // Champs utilisés pour la connexion
  authForm: FormGroup;

  // Erreurs retournées par le serveur pour le champ du courriel
  emailServerError = '';

  // Erreurs retournées par le serveur pour le champ du mot de passe
  passwordServerError = '';

  // Si des communications avec le serveur sont en cours
  isSubmitting = false;

  // Surveille la largeur courante de l'écran de l'utilisateur
  mobileQuery: MediaQueryList;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private userService: UserService,
    private formBuilder: FormBuilder,
    private media: MediaMatcher,
    private authService: AuthService
  ) {
    // Le changement de mobile à plein écran s'effectue lorsque l'écran fait 960 pixels de large
    this.mobileQuery = this.media.matchMedia('(min-width: 960px)');

    // Instanciation des champs
    this.authForm = this.formBuilder.group({
      // Le champ du courriel doit avoir la forme d'un courriel et est requis
      'email': ['', [Validators.required, Validators.email]],

      // Le champ du mot de passe et est requis
      'password': ['', Validators.required]
    });
  }

  /**
   * Obtention d'un token de l'API avec les informations de connexion de l'utilisateur.
   */
  login() {
    // Si le courriel et le mot de passe sont valides, procéder à l'authentification
    if (this.authForm.valid) {

      // Désactiver les champs lors de l'envoit de la requête d'authentification
      this.isSubmitting = true;

      this.userService
        .attemptAuth(this.authForm.value)
        .subscribe(
          // Si l'authentification est un succès, naviguer à la page principale
          () => this.router.navigateByUrl('/'),

          /*
          * Si l'authentification échoue :
          * Rendre les champs de connexion disponibles
          * Afficher les champs de connexion comme incorrects
          * Assigner le message du serveur
          * */
          () => {
            this.isSubmitting = false;
            this.authForm.controls['email'].setErrors([]);
            this.authForm.controls['password'].setErrors([]);
            this.emailServerError = 'Courriel incorrect';
            this.passwordServerError = 'Mot de passe incorrect';
          }
        );
    }
  }

  /**
   * Obtention d'un token de l'API avec Facebook.
   */
  loginFacebook(): void {
    // Ne rien faire si une communication est déjà en cours avec le serveur.
    if (!this.isSubmitting) {

      // Désactiver les champs lors de l'envoit de la requête d'authentification
      this.isSubmitting = true;

      this.authService.signIn(FacebookLoginProvider.PROVIDER_ID)
        .then(
          // Si les communications avec Facebook sont un succès, le token Facebook est envoyé à l'API
          (user) => {

            this.userService
              .attemptAuthFacebook(user.authToken)
              .subscribe(
                // Si l'authentification est un succès, naviguer à la page principale
                () => this.router.navigateByUrl('/'),

                // En cas d'erreur, rien n'est fait, mais il est de nouveau possible d'intéragir avec l'API
                () => this.isSubmitting = false
              );
          },
          // En cas d'erreur, rien n'est fait, mais il est de nouveau possible d'intéragir avec l'API
          () => this.isSubmitting = false);
    }
  }

  /**
   * Obtention d'un token de l'API avec Google.
   */
  loginGoogle(): void {
    // Ne rien faire si une communication est déjà en cours avec le serveur.
    if (!this.isSubmitting) {

      // Désactiver les champs lors de l'envoit de la requête d'authentification
      this.isSubmitting = true;

      this.authService.signIn(GoogleLoginProvider.PROVIDER_ID)
        .then(
          // Si les communications avec Google sont un succès, le token Facebook est envoyé à l'API
          (user) => {
            this.userService
              .attemptAuthGoogle(user.idToken)
              .subscribe(
                // Si l'authentification est un succès, naviguer à la page principale
                () => this.router.navigateByUrl('/'),

                // En cas d'erreur, rien n'est fait, mais il est de nouveau possible d'intéragir avec l'API
                () => this.isSubmitting = false
              );
          },
          // En cas d'erreur, rien n'est fait, mais il est de nouveau possible d'intéragir avec l'API
          () => this.isSubmitting = false);
    }
  }

  /**
   * Obtenir l'erreur du champ du courriel.
   * @return Chaîne de caractères de l'erreur courante
   */
  getEmailErrorMessage(): string {
    if (this.emailServerError !== '') {
      return this.emailServerError;
    } else if (this.authForm.controls['email'].hasError('required')) {
      return 'Le courriel est requis.';
    } else if (this.authForm.controls['email'].hasError('email')) {
      return 'Courriel non valide.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur du champ du mot de passe.
   * @return Chaîne de caractères de l'erreur courante
   */
  getPasswordErrorMessage(): string {
    if (this.passwordServerError !== '') {
      return this.passwordServerError;
    } else if (this.authForm.controls['password'].hasError('required')) {
      return 'Le mot de passe est requis.';
    } else {
      return '';
    }
  }

}
