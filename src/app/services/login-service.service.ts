import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Http } from '@angular/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
interface myData {
  message: string;
}

@Injectable({
  providedIn: 'root'
})

export class LoginServiceService {
 
  // logIn() {
  //   return this.http.get<myData>('http://localhost/api/test.php');
  // }

  // constructor(private http: HttpClient) { }

  indexNo:string="";
  isloggedin:boolean=false;
  constructor(private http:Http) {
    console.log("connected Login");
  }

  setLoggedin(logged){
     this.isloggedin=logged;
  }
  login(index_signin,password_signin){
    var headers= new Headers();
    headers.append('Content-Type','application/X-www-form=urlencoded');

    return this.http.post("http://localhost/Ess/api/login.php",{"index_signin":index_signin,"password_signin":password_signin}).pipe(map(res=>res.json()));
  }
  getDetails(){
    return this.http.post("http://localhost/Ess/api/user.php",{"indexNo":this.indexNo}).pipe(map(res=>res.json()));
  }
  setIndex(index_signin){
    this.indexNo=index_signin;
  }
  getIndex(){
    return this.indexNo;
  }
  updateDetails(indexNo,firstname,lastname,password){
    return this.http.post("http://localhost/Ess/api/update.php",{"indexno":indexNo,"firstname":firstname,"lastname":lastname,"password":password}).pipe(map(res=>res.json()));
  }
  
  private _errorHandler(error:Response){
    console.error("Error Occured:"+error);
    return Observable.throw(error||"Some error occured in Server");
  }
}
