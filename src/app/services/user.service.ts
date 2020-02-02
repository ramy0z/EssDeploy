import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';
@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(private http:HttpClient) { }
  
  signUp(RData){
    let reqData={
      "name":"signUser",
      "param":RData
    };
    return this.http.post(`${environment.apiUrl}`,reqData).pipe(map(res=>res));
  }

  getPlaceSrv(type , id){
    if(type=='country' ||  type=='state'  || type=='city' ){
      let reqData={
        "name":"ProcessPlaces",
        "param":{"type":type }
      };
      if( type=='state'  || type=='city'){
        reqData['param']['id']=id;
      }
      return this.http.post(`${environment.apiUrl}`,reqData).pipe(map(res=>res));
    }
  }

  private _errorHandler(error:Response){
    console.error("Error Occured:"+error);
    return Observable.throw(error||"Some error occured in Server");
  }
}
