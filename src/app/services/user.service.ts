import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { config } from '../config';
@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(private http:Http) { }
  
  signUp(RData){
    return this.http.post(`${config.apiUrl}/register`,RData).pipe(map(res=>res.json()));
  }


  private _errorHandler(error:Response){
    console.error("Error Occured:"+error);
    return Observable.throw(error||"Some error occured in Server");
  }
}
