import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { of, Observable } from 'rxjs';
import { catchError, mapTo, tap, map } from 'rxjs/operators';
import { config } from './../config';
import { Tokens } from '../models/tokens';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private readonly JWT_TOKEN = 'JWT_TOKEN';
  private readonly REFRESH_TOKEN = 'REFRESH_TOKEN';
  private readonly UID = 'UID';
  private readonly UROLE = 'UROLE';
  private loggedUser: string;

  constructor(private http: HttpClient) {
  }
  login(user): Observable<boolean> {
    
    let httpOptions = {
      headers: new HttpHeaders({'Content-Type': 'application/json',
       'Access-Control-Allow-Origin': '*' , 
      'Access-Control-Allow-Methods': 'POST',
      'Access-Control-Allow-Headers': 'X-PINGOTHER, Content-Type',
      'Access-Control-Max-Age': '86400'})
    };

    let obj={
      "name":"login",
      "param":{
        "userId":user.username,
        "pass":user.password
      }
    }
    //`${config.apiUrl}`
    return this.http.post<any>(`${config.apiUrl}`, obj ,httpOptions )
      .pipe(
        tap(response => this.doLoginUser('uname',response['response']['message'])),
        mapTo(true),
        catchError(error => {
          return of(false);
        })
        );
  }

  logout() {
     this.doLogoutUser();
  }

  isLoggedIn() {
    var res=this.getJwtToken()
    if(res==undefined || res==null || res=='')return false;
    return true;
  }

  refreshToken() {
    return this.http.post<any>(`${config.apiUrl}`, {
      'refreshToken': this.getRefreshToken()
    }).pipe(tap((tokens: Tokens) => {
      this.storeJwtToken(tokens.JWT_TOKEN);
    }));
  }

  getJwtToken() {
    return localStorage.getItem(this.JWT_TOKEN);
  }

  private doLoginUser(username: string, tokens: Tokens) {
    this.loggedUser = username;
    this.storeTokens(tokens);
  }

  private doLogoutUser() {
    this.loggedUser = null;
    this.removeTokens();
  }

  private getRefreshToken() {
    return localStorage.getItem(this.REFRESH_TOKEN);
  }

  private storeJwtToken(token: string) {
    localStorage.setItem(this.JWT_TOKEN, token);
  }

  private storeTokens(tokens: Tokens) {
    localStorage.setItem(this.JWT_TOKEN, tokens.JWT_TOKEN);
    localStorage.setItem(this.REFRESH_TOKEN, tokens.REFRESH_TOKEN);
    localStorage.setItem(this.UID, tokens.UID);
    localStorage.setItem(this.UROLE, tokens.UROLE);
  }

  private removeTokens() {
    localStorage.removeItem(this.JWT_TOKEN);
    localStorage.removeItem(this.REFRESH_TOKEN);
    localStorage.removeItem(this.UID);
    localStorage.removeItem(this.UROLE);
  }



  // private handleError(error: HttpErrorResponse) {
  //   if (error.error instanceof ErrorEvent) {
  //     // A client-side or network error occurred. Handle it accordingly.
  //     console.error('An error occurred:', error.error.message);
  //   } else {
  //     // The backend returned an unsuccessful response code.
  //     // The response body may contain clues as to what went wrong,
  //     console.error(
  //       `Backend returned code ${error.status}, ` +
  //       `body was: ${error.error}`);
  //   }
  //   // return an observable with a user-facing error message
  //   return throwError(
  //     'Something bad happened; please try again later.');
  // }


}
