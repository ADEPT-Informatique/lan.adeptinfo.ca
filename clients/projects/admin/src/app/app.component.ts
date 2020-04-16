import {ChangeDetectorRef, Component, OnInit} from '@angular/core';
import {MediaMatcher} from '@angular/cdk/layout';
import {UserService} from 'core';
import {of} from 'rxjs';
import {Router} from '@angular/router';
import {User} from 'core';
import {LanService} from 'core';
import {environment} from '../environments/environment';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
/**
 * Gestion des menus, affichage de l'écran courant, et affichage du pied de page.
 */
export class AppComponent implements OnInit {

  // Surveille la largeur courante de l'écran de l'utilisateur
  mobileQuery: MediaQueryList;

  // Utilisateur courant de l'application
  currentUser: User;

  constructor(
    private userService: UserService,
    private lanService: LanService,
    changeDetectorRef: ChangeDetectorRef,
    private media: MediaMatcher,
    private router: Router
  ) {
  }

  ngOnInit(): void {

    // S'abonner aux changements d'authentification dans l'application
    this.userService.isAuthenticated.subscribe(
      (authenticated) => {
        // Redirection vers l'écran de connexion si aucuns utilisateur n'est connecté
        if (!authenticated) {
          this.router.navigateByUrl('/login');
          return of(null);
        } else {
          this.router.navigateByUrl('/');
        }
      }
    );

    // S'abonner aux changements de LAN courant pour obtenir les permissions de l'utilisateur
    this.lanService.currentLan.subscribe(
      (currentLan) => {
        // Redirection vers l'écran de connection si aucuns utilisateur n'est connecté
        this.userService.populate(currentLan.id);
      }
    );

    // Le changement de mobile à plein écran s'effectue lorsque l'écran fait 600 pixels de large
    this.mobileQuery = this.media.matchMedia('(min-width: 600px)');

    // Obtenir le sommaire de l'utilisateur
    this.userService.populate();

    // S'abonner aux changements d'utilisateur courant
    this.userService.currentUser.subscribe(
      (userData) => {
        this.currentUser = userData;
      }
    );
  }

  /**
   * Déconnexion de l'utilisateur courant.
   */
  logout(): void {

    // Déconnexion de l'utilisateur
    this.userService.logout();

    // Navigation vers l'écran de connexion
    this.router.navigateByUrl('/login');

  }

  /**
   * Obtenir l'URL du site principal.
   */
  getPlayerUrl() {
    return environment.playerUrl;
  }
}
