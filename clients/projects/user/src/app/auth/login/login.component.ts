import { Component, ChangeDetectorRef } from '@angular/core';
import { FormGroup,  FormBuilder,  Validators } from '@angular/forms';
import { UserService} from 'projects/core/src/public_api';
import { MediaMatcher } from '@angular/cdk/layout';
import { Router } from '@angular/router';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent{

  authForm : FormGroup;
  emailServerError = '';
  passwordServerError = '';
  mobileQuery: MediaQueryList;
  passwordFocusLoss = false;
  emailFocusLoss = false;

  constructor(
    private userService: UserService,
    private formBuilder: FormBuilder,
    changeDetectorRef: ChangeDetectorRef,
    private media: MediaMatcher,
    private router: Router) {
      this.mobileQuery = this.media.matchMedia('(min-width: 960px)');
      this.authForm = this.formBuilder.group({
        // Le champ du courriel doit avoir la forme d'un courriel et est requis
        'email': ['',[Validators.required, Validators.email]],
  
        // Le champ du mot de passe et est requis
        'password': ['', Validators.required]
      });
  }

  login(){
    // Ne procède pas à l'authentification si le formulaire n'est pas valide.
    if(!this.authForm.valid) return;
    console.log("Clicked!")
    this.userService.attemptAuth(this.authForm.value).subscribe(

                // Si l'authentification est un succès, naviguer à la page principale
                (response) => { this.router.navigateByUrl('/'); },

                /*
                * Si l'authentification échoue :
                * Rendre les champs de connexion disponibles
                * Afficher les champs de connexion comme incorrects
                * Assigner le message du serveur
                * */
                (error) => {console.log(error);
                  this.authForm.controls['password'].setErrors([]);
                  this.passwordServerError = 'Mot de passe incorrect';
                }
    )
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
