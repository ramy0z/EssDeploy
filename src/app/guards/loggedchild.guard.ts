import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot, CanActivateChild } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable()
export class LoggedChildGuard implements CanActivateChild {
    canActivateChild(route: ActivatedRouteSnapshot, state: RouterStateSnapshot):  boolean {
        return this.canActivate(route, state);
   }
    constructor(private authService: AuthService, private router: Router) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): any{
        if(!this.authService.isLoggedIn())
        {
            this.router.navigate(['/login'])
        }
        return this.authService.isLoggedIn();
        
    }
}
