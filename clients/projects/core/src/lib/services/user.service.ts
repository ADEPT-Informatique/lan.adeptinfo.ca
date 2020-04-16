import {Injectable} from '@angular/core';
import {BehaviorSubject, Observable, ReplaySubject} from 'rxjs';

import {distinctUntilChanged, map} from 'rxjs/operators';
import {User} from '../models/api/user';
import {ApiService} from './api.service';
import {JwtService} from './jwt.service';
import {parameters} from '../params';
import {HttpParams} from '@angular/common/http';

@Injectable()
/**
 * Actions liées aux utilisateur et leur authentification dans l'application.
 */
export class UserService {

  // Observables de l'utilisateur courant
  private currentUserSubject = new BehaviorSubject<User>({} as User);
  public currentUser = this.currentUserSubject.asObservable().pipe(distinctUntilChanged());

  // Observable de l'état de connexion d'un utilisateur
  private isAuthenticatedSubject = new ReplaySubject<boolean>(1);
  public isAuthenticated = this.isAuthenticatedSubject.asObservable();

  constructor(
    private apiService: ApiService) {
  }

  /**
   * Obtenir les détails de l'utilisateur.
   */
  populate(lanId?: number): void {
    // Si un JWT existe dans le localstorage, tenter d'obtenir le sommaire de l'utilisateur
    if (JwtService.getToken()) {

      const params = new HttpParams();

      if (lanId != null) {
        params.append('lan_id', lanId.toString());
      }

      this.apiService.get('/admin/summary', params)
        .subscribe(
          // Si l'appel est un succès, mettre les données reçues dans l'utilisateur courant
          data => this.setAuth(data),

          // Si l'appel échoue, supprimer les informations de l'utilisateur pour qu'il s'authentifie à nouveau
          () => this.purgeAuth()
        );
    } else {
      // Retirer ce qui pourait rester dans la mémoire de l'application de l'utilisateur précédent
      this.purgeAuth();
    }
  }

  /**
   * Détails de l'authentification.
   * @param user Utilisateur authentifié
   */
  setAuth(user: User): void {

    // Rendre les données de l'utilisateur courant observables
    this.currentUserSubject.next(user);

    // Vider la valeur
    this.isAuthenticatedSubject.next();

    // Mettre isAuthenticated à true
    this.isAuthenticatedSubject.next(true);

  }

  /**
   * Supprimer toute traces de l'utilisateur dans le localstorage et dans la mémoire.
   */
  purgeAuth(): void {

    // Supprimer le JWT du localstorage
    JwtService.destroyToken();

    // Retirer l'utilisateur courant
    this.currentUserSubject.next({} as User);

    // Mettre le statut d'authentification à false
    this.isAuthenticatedSubject.next(false);

  }

  /**
   * Tentative d'obtention d'un JWT à l'API.
   * @param credentials Informations de l'utilisateur qui tente de se connecter
   */
  attemptAuth(credentials: any): Observable<string> {
    return this.apiService.post('/oauth/token', {

      // Type d'authentification de l'API
      grant_type: parameters.grantType,

      // Id du client d'authentification de l'API
      client_id: parameters.clientId,

      // Mot de passe du client d'authentification de l'API
      client_secret: parameters.clientSecret,

      // Courriel de l'utilisateur
      username: credentials.email,

      // Mot de passe de l'utilsateur
      password: credentials.password
    })
      .pipe(map(
        data => {

          // Sauvegarder le JWT renvoyé du serveur dans le localstorage
          JwtService.saveToken(data.access_token);

          // Obtenir les informations sommaires de l'utilisateur nouvellement connecté
          this.populate();

          // Retourner le token d'accès à l'API
          return data.access_token;
        }
      ));
  }

  /**
   * Tentative d'obtention d'un JWT à l'API avec un token Facebook.
   * @param token Token envoyé par Facebook
   */
  attemptAuthFacebook(token: string): Observable<string> {
    return this.apiService.post('/user/facebook', {

      // Token envoyé par Facebook
      access_token: token
    })
      .pipe(map(
        data => {

          // Sauvegarder le JWT renvoyé du serveur dans le localstorage
          JwtService.saveToken(data.token);

          // Obtenir les informations sommaires de l'utilisateur nouvellement connecté
          this.populate();

          // Retourner le token d'accès à l'API
          return data.token;
        }
      ));
  }

  /**
   * Tentative d'obtention d'un JWT à l'API avec un token Google.
   * @param token Token envoyé par Google
   */
  attemptAuthGoogle(token: string): Observable<string> {
    return this.apiService.post('/user/google', {

      // Token envoyé par Google
      access_token: token
    })
      .pipe(map(
        data => {

          // Sauvegarder le JWT renvoyé du serveur dans le localstorage
          JwtService.saveToken(data.token);

          // Obtenir les informations sommaires de l'utilisateur nouvellement connecté
          this.populate();

          // Retourner le token d'accès à l'API
          return data.token;
        }
      ));
  }

  /**
   * Déconnecter un utilisateur courant de l'application en local et dans l'API.
   */
  logout(): void {
    // Envoyer une requête pour supprimer le token dans l'API
    this.apiService.post('/user/logout', {});

    // Supprimer toute traces de l'utilisateur en local
    this.purgeAuth();
  }

  /**
   * Obtenir les détails de l'utilisateur courant.
   */
  getCurrentUser(): User {
    return this.currentUserSubject.value;
  }
}
