import { Component, OnInit } from "@angular/core";
import {
  FormGroup,
  FormBuilder,
  Validators,
  AbstractControl
} from "@angular/forms";
import { UserService } from "projects/core/src/public_api";
import { Router } from "@angular/router";
import { MediaMatcher } from "@angular/cdk/layout";

@Component({
  selector: "app-register",
  templateUrl: "./register.component.html",
  styleUrls: ["./register.component.css"]
})
export class RegisterComponent implements OnInit {
  authForm: FormGroup;
  emailServerError = "";
  passwordServerError = "";
  confrmPasswordServerError = "";
  mobileQuery: MediaQueryList;
  passwordFocusLoss = false;
  emailFocusLoss = false;

  constructor(
    private media: MediaMatcher,
    private formBuilder: FormBuilder,
    private userService: UserService,
    private router: Router
  ) {
    this.mobileQuery = this.media.matchMedia("(min-width: 960px)");

    this.authForm = this.formBuilder.group({
      first_name: ["", Validators.required],

      last_name: ["", Validators.required],
      // Le champ du courriel doit avoir la forme d'un courriel et est requis
      email: ["", [Validators.required, Validators.email, Validators.pattern("[\\w\\d]+[\\w\\d.]+[\\w\\d]@[\\w]+\\.[\\w]*\\.?[\\w]+")]],
      // Le champ du courriel doit avoir la forme d'un courriel et est requis
      password: ["", Validators.required],

      // Le champ du mot de passe et est requis
      confrmPassword: ["", Validators.required]
    });
  }

  ngOnInit() {
    console.log(this.authForm.value[1])
  }

  submit() {
    
    // Ne procède pas à l'authentification si le formulaire n'est pas valide.
    
    if (!this.authForm.valid || this.authForm.value['password'] != this.authForm.value['confrmPassword']){
      if (this.authForm.value['email']){

      }

      if (this.authForm.value['password'] != this.authForm.value['confrmPassword']){
        this.authForm.controls['confrmPassword'].setErrors([])
        this.confrmPasswordServerError = "Les deux mots de passe ne correspondent pas."
        return;
      }
      return;
    }

    // 
    this.userService.attemptSignup(this.authForm.value).subscribe(
      // Si la creation de compte est un succes, envoie a la page de confirmation.
      response => {
       this.router.navigateByUrl("confirm");
      },

      /*
       * Si l'authentification échoue :
       * Rendre les champs de connexion disponibles
       * Afficher les champs de connexion comme incorrects
       * Assigner le message du serveur
       * */
      error => {
        if (error.message.password){
          this.authForm.controls["password"].setErrors([]);
          this.passwordServerError = error.message.password;
        }
        if (error.message.email){
          this.authForm.controls["email"].setErrors([]);
          this.emailServerError = error.message.email;
          
        }
      }
    );
  }

  getNomErrorMessage() {
    return "Il est obligatoire de stipuler ce champs."
  }
  getEmailErrorMessage() {
    return this.emailServerError
  }
  getPasswordErrorMessage() {
    return this.passwordServerError
  }
  getConfrmPasswordErrorMessage() {
    return this.confrmPasswordServerError;
  }
}
