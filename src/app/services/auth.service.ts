import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { of, Observable } from 'rxjs';
import { catchError, mapTo, tap, map } from 'rxjs/operators';
import { Tokens } from '../models/tokens';
import { environment } from '../../environments/environment';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private readonly JWT_TOKEN = 'JWT_TOKEN';
  private readonly REFRESH_TOKEN = 'REFRESH_TOKEN';
  private readonly UID = 'UID';
  private readonly UROLE = 'UROLE';
  private readonly UNAME ='UNAME';

  constructor(private router:Router,private http: HttpClient) {
  }
  login(user): Observable<boolean> {
    let obj={"name":"login","param":{"userId":user.username,"pass":user.password} };
    return this.http.post<any>(`${environment.apiUrl}`, obj)
      .pipe(
        map(data => {
          if(data['response']['status']==200) {
            localStorage.setItem(this.UNAME, data['response']['data'].UNAME);
            this.doLoginUser(data['response']['data']);
            return true;
          }
          else return false;
        },
        error =>{ return false;}
        )
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
    let reqDat={"name":"userAuth","param":{"uid": this.getUId(),"refresh": this.getRefreshToken()} }
    return this.http.post<any>(`${environment.apiUrl}`,reqDat).pipe(
      tap(data => {
          if(data['response']['status']==200) {
            this.storeJwtToken(data['response']['data'].JWT_TOKEN);
            //return data['response']['data'].JWT_TOKEN;
          }else{
            this.doLogoutUser();
          }
      }),
      map(data => data['response']['data'].JWT_TOKEN)
      );
  }

  getJwtToken() {
    return localStorage.getItem(this.JWT_TOKEN);
  }

  private doLoginUser(tokens: Tokens) {
    this.storeTokens(tokens);
  }

  private doLogoutUser() {
    this.removeTokens();
    this.router.navigate(['/login']);
  }

  private getRefreshToken() {
    return localStorage.getItem(this.REFRESH_TOKEN);
  }
  private getUId() {
    return localStorage.getItem(this.UID);
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
    // localStorage.removeItem(this.JWT_TOKEN);
    // localStorage.removeItem(this.REFRESH_TOKEN);
    // localStorage.removeItem(this.UID);
    // localStorage.removeItem(this.UROLE);
    localStorage.clear();
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
