import {Injectable} from '@angular/core';
import {parameters} from '../params';
import {HttpClient, HttpParams} from '@angular/common/http';
import {Observable, throwError} from 'rxjs';
import {catchError} from 'rxjs/operators';

@Injectable()
/**
 * Standardisation des communications HTTP avec les resources externes.
 */
export class ApiService {

  constructor(
    private http: HttpClient
  ) {
  }

  /**
   * Formatage des erreurs retournées par une requête
   * @param error Erreurs
   */
  private static formatErrors(error: any) {
    return throwError(error.error);
  }

  /**
   * Effectuer une requête GET.
   * @param path Chemin de la requête
   * @param params Paramètres de la requête
   * @param baseUrl Url de base de la requête
   */
  get(path: string, params: HttpParams = new HttpParams(), baseUrl = parameters.apiUrl): Observable<any> {
    return this.http.get(baseUrl + path, {params})
      .pipe(catchError(ApiService.formatErrors));
  }

  /**
   * Effectuer une requête PUT.
   * @param path Chemin de la requête
   * @param body Paramètres du corps de la requête
   * @param baseUrl Url de base de la requête
   */
  put(path: string, body: Object = {}, baseUrl = parameters.apiUrl): Observable<any> {
    return this.http.put(
      baseUrl + path,
      JSON.stringify(body)
    ).pipe(catchError(ApiService.formatErrors));
  }

  /**
   * Effectuer une requête POST.
   * @param path Chemin de la requête
   * @param body Paramètres du corps de la requête
   * @param baseUrl Url de base de la requête
   */
  post(path: string, body: Object = {}, baseUrl = parameters.apiUrl): Observable<any> {
    return this.http.post(
      baseUrl + path,
      JSON.stringify(body)
    ).pipe(catchError(ApiService.formatErrors));
  }

  /**
   * Effectuer une requête DELETE.
   * @param path Chemin de la requête
   * @param params Paramètres de la requête
   * @param baseUrl Url de base de la requête
   */
  delete(path: string, params: HttpParams = new HttpParams(), baseUrl = parameters.apiUrl): Observable<any> {
    return this.http.delete(
      baseUrl + path
    ).pipe(catchError(ApiService.formatErrors));
  }
}
