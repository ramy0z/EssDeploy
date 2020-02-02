import { Component, OnDestroy, Inject, OnInit } from '@angular/core';
import { DOCUMENT } from '@angular/common';
import { navItemsEn, navItemsAr } from '../../_nav';
import { TranslateService } from '@ngx-translate/core';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-dashboard',
  templateUrl: './default-layout.component.html'
})
export class DefaultLayoutComponent implements OnDestroy  {
  
  public navItems = navItemsAr;

  public sidebarMinimized = true;
  private changes: MutationObserver;
  public element: HTMLElement;
  constructor(private authService: AuthService, private router: Router,private translate: TranslateService , @Inject(DOCUMENT) _document?: any) {

    this.changes = new MutationObserver((mutations) => {
      this.sidebarMinimized = _document.body.classList.contains('sidebar-minimized');
    });
    this.element = _document.body;
    this.changes.observe(<Element>this.element, {
      attributes: true,
      attributeFilter: ['class']
    });
  }
  useLanguage(language: string) {
    this.translate.use(language);
    this.navItems = (language=="ar")?navItemsAr:navItemsEn;
  }
  ngOnDestroy(): void {
    this.changes.disconnect();
  }
  logout(){
    localStorage.clear();
    this.router.navigate(['/login']);
  }
}
