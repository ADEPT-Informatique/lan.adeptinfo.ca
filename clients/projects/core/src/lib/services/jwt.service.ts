import {Injectable} from '@angular/core';

@Injectable()
/**
 * Actions liées aux JWT (JSON Web Token)
 */
export class JwtService {

  /**
   * Obtenir le token d'accès gardé dans le localstorage.
   * @return token de l'utilisateur
   */
  static getToken(): string {
    return window.localStorage['jwtToken'];
  }

  /**
   * Mettre un token d'accès dans le localstorage.
   * @param token Token à conserver
   */
  static saveToken(token: string): void {
    window.localStorage['jwtToken'] = token;
  }

  /**
   * Supprimer le token d'accès du localstorage.
   */
  static destroyToken(): void {
    window.localStorage.removeItem('jwtToken');
  }

}
