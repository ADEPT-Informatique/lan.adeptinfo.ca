import {Pipe, PipeTransform} from '@angular/core';
import { UserService } from 'projects/core/src/public_api';

@Pipe({name: 'HasPermission'})
/**
 * Déterminer si l'utilisateur courant possède une permission.
 */
export class HasPermissionPipe implements PipeTransform {

  constructor(private userService: UserService) {
  }

  /**
   * Déterminer si l'utilisateur possède une permission.
   * @param permission Nom de la permission de l'API
   * @return Si l'utilisateur possède la permission
   */
  transform(permission: string): boolean {

    // Si les permissions n'ont pas encore été chargées, retourner faux
    if (this.userService.getCurrentUser().permissions == null) {
      return false;
    }

    // Rechercher s'il existe une permission qui possède le nom de la permission passée en paramètres
    return this.userService
      .getCurrentUser()
      .permissions
      .some(e => e.name === permission);
  }
}
