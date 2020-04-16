import {Injectable} from '@angular/core';
import { User } from '../models/api/user';

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
   * Verifier si il y a une token dans le localstorage.
   * 
   */
  static checkToken():boolean{
    return window.localStorage['jwtToken'] != '' && window.localStorage['jwtToken'] != null;
  }

  /**
   * Supprimer le token d'accès du localstorage.
   */
  static destroyToken(): void {
    window.localStorage.removeItem('jwtToken');
  }

  /**
   * Obtenir l'utilisateur connecté gardé dans le localstorage.
   * @return l'utilisateur
   */
  static getUser(): User {
    return JSON.parse(window.localStorage['appUser']);
  }

  /**
   * Mettre l'utilisateur connecté dans le localstorage.
   * @param user L'utilisateur conserver
   */
  static saveUser(user: User): void {
    window.localStorage['appUser'] = JSON.stringify(user);
  }

  /**
   * Verifier si il y a une l'utilisateur dans le localstorage.
   * 
   */
  static checkUser():boolean{
    return window.localStorage['appUser'] != '' || window.localStorage['appUser'] != null;
  }

  /**
   * Supprimer l'utilisateur connecté du localstorage.
   */
  static destroyUser(): void {
    window.localStorage.removeItem('appUser');
  }
}
