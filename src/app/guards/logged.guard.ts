import { Injectable } from '@angular/core';
import { CanActivate, Router, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable()
export class LoggedGuard implements CanActivate {
    constructor(private authService: AuthService, private router: Router) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): any{
        if(!this.authService.isLoggedIn())
        {
            this.router.navigate(['/login'])
        }
        return this.authService.isLoggedIn();
        
    }
}
