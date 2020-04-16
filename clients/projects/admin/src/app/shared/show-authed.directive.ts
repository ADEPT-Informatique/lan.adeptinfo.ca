import {Directive, Input, OnInit, TemplateRef, ViewContainerRef} from '@angular/core';
import { UserService } from 'projects/core/src/public_api';

@Directive({selector: '[appShowAuthed]'})
/**
 * Directive pour permettre de montrer ou de cacher un élément selon l'état d'authentification d'un utilisateur.
 *
 * Erreur possible : Si un élément s'affiche à plusieurs reprises, c'est que l'état de l'authentification
 * est appelé plusieurs fois de suite.
 */
export class ShowAuthedDirective implements OnInit {

  // Si l'élément doit être montré ou caché si l'utilisateur est authentifié
  condition: boolean;

  constructor(
    private templateRef: TemplateRef<any>,
    private userService: UserService,
    private viewContainer: ViewContainerRef
  ) {
  }

  ngOnInit() {

    // S'abonner aux changement d'états d'authentification de l'utilisateur
    this.userService.isAuthenticated.subscribe(
      (isAuthenticated) => {

        /*
          Conditions pour afficher un élément :
          L'utilisateur est authentifié et il demande à voir l'élément
          ou
          L'utilisateur n'est pas authentifié et il demande à cacher l'élément
         */
        if (isAuthenticated && this.condition || !isAuthenticated && !this.condition) {
          // Afficher l'élément en question
          this.viewContainer.createEmbeddedView(this.templateRef);
        } else {
          this.viewContainer.clear();
        }
      }
    );
  }

  @Input() set appShowAuthed(condition: boolean) {
    this.condition = condition;
  }

}
