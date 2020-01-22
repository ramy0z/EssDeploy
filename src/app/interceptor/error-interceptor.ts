import {
    HttpEvent,
    HttpInterceptor,
    HttpHandler,
    HttpRequest,
    HttpResponse,
    HttpErrorResponse
} from '@angular/common/http';
import {inject,Injectable} from '@angular/core'
import { Observable, throwError, } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { ErrorDialogService } from '../containers/error-dialog/errordialog.service';

import {Router} from '@angular/router'
@Injectable()
export class HttpErrorInterceptor implements HttpInterceptor {
    constructor(private route:Router,private errorDialogService:ErrorDialogService){} 
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        return next.handle(request)
            .pipe(
                catchError((error: HttpErrorResponse) => {
                    let res_server=(error.error.error !=undefined)?error.error.error: {};
                    let errMsg = '';
                    // Client Side Error
                    if (error.error instanceof ErrorEvent) {
                        errMsg = `Error: ${error.error.message}`;
                        res_server.title='Client Side ERROR';
                        res_server.message='Client error occurred. Please Reload Your Browser.';
                        this.errorDialogService.openDialog(res_server);
                    }
                    else {  // Server Side Error
                        //  console.log(error)
                         if(error.status==0)
                         {  res_server.title='NETWORK CONNECTION ERROR';
                            res_server.status='0';
                            res_server.message='A server error occurred.  Please Reload Your Browser.';
                            this.errorDialogService.openDialog(res_server);
                         }
                         else{
                            if (res_server.status <200 && res_server.status >=100) {
                                res_server.title='Requested Data NOT VALID';
                            }else if(res_server.status <303 && res_server.status >=300){
                                res_server.title='Authorization Processing ERROR';
                            }

                            this.errorDialogService.openDialog(res_server);
                         }
                        // if (error.status == 500) {
                        //     errMsg = `Error Code: ${error.status},  Message: ${error.message}`;
                        // }
                        // if (error.status == 404) {
                        //     errMsg = `Error Code: ${error.status},  Message: ${error.message}`;
                        // }
                        // if (error.status == 401) {
                        //     errMsg = `Error Code: ${error.status},  Message: ${error.message}`;
                        // }
                        // if(error.status==403)
                        // {
                        //     console.log(error.status)
                        //     localStorage.clear();
                        //     this.route.navigate(['/login'])
                        // }
                        
                    }
                    // return an observable
                    return throwError(res_server);
                })
            )
    }

} 