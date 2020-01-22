import { BrowserModule } from '@angular/platform-browser';
// import ngx-translate and the http loader
import {TranslateLoader, TranslateModule} from '@ngx-translate/core';
import {TranslateHttpLoader} from '@ngx-translate/http-loader';
import {HttpClient, HttpClientModule} from '@angular/common/http';

import { NgModule } from '@angular/core';
import { LocationStrategy, HashLocationStrategy } from '@angular/common';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { PerfectScrollbarConfigInterface } from 'ngx-perfect-scrollbar';

const DEFAULT_PERFECT_SCROLLBAR_CONFIG: PerfectScrollbarConfigInterface = {
  suppressScrollX: true
};

import { AppComponent } from './app.component';

// Import containers
import { DefaultLayoutComponent } from './containers';

import { P404Component } from './views/error/404.component';
import { P500Component } from './views/error/500.component';
import { LoginComponent } from './views/login/login.component';
import { RegisterComponent } from './views/register/register.component';

const APP_CONTAINERS = [
  DefaultLayoutComponent
];

import {
  AppAsideModule,
  AppBreadcrumbModule,
  AppHeaderModule,
  AppFooterModule,
  AppSidebarModule,
} from '@coreui/angular';

// Import routing module
import { AppRoutingModule } from './app.routing';

// Import 3rd party components
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { ChartsModule } from 'ng2-charts';
import { HttpModule } from '@angular/http';
import { HTTP_INTERCEPTORS } from '@angular/common/http';
import { FormsModule ,ReactiveFormsModule } from '@angular/forms';
import { CustomFormsModule } from 'ng2-validation';

import { AuthGuard } from './guards/auth.guard';
import { AuthService } from './services/auth.service';
import { MyOrderComponent } from './views/orders/myorder.component';
import { MyaccountComponent } from './views/accounts/myaccount/myaccount.component';
import { SecurityInfoComponent } from './views/accountSetting/security-info/security-info.component';
import { MywalletComponent } from './views/accounts/mywallet/mywallet.component';
import { ProfileComponent } from './views/accountSetting/profile/profile.component';
import { MyorderhistoryComponent } from './views/orders/myorderhistory/myorderhistory.component';
import { OrderPrintComponent } from './views/orders/order-print/order-print.component';
import { AboutUsComponent } from './views/pages/about-us/about-us.component';
import { FaqComponent } from './views/pages/faq/faq.component';
import { PickupLocationsComponent } from './views/pages/pickup-locations/pickup-locations.component';
import { PrivacyPolicyComponent } from './views/pages/privacy-policy/privacy-policy.component';
import { TermsConditionsComponent } from './views/pages/terms-conditions/terms-conditions.component';
import { ContactUsComponent } from './views/pages/contact-us/contact-us.component';
import { LoggedGuard } from './guards/logged.guard';
import { LoggedChildGuard } from './guards/loggedchild.guard';
import { TokenInterceptor } from './interceptor/token.interceptor';
import { HttpErrorInterceptor } from './interceptor/error-interceptor';
import { MatDialogModule } from '@angular/material';
import { ErrorDialogService } from './containers/error-dialog/errordialog.service';

 @NgModule({
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    MatDialogModule,
    AppRoutingModule,
    AppAsideModule,
    AppBreadcrumbModule.forRoot(),
    AppFooterModule,
    AppHeaderModule,
    AppSidebarModule,
    PerfectScrollbarModule,
    BsDropdownModule.forRoot(),
    TabsModule.forRoot(),
    ChartsModule,
    FormsModule,ReactiveFormsModule,
    CustomFormsModule,
    HttpModule,
     // ngx-translate and the loader module
     HttpClientModule,
     TranslateModule.forRoot({
         loader: {
             provide: TranslateLoader,
             useFactory: HttpLoaderFactory,
             deps: [HttpClient]
         }
     })
    //  ,JwtModule.forRoot({
    //   config:{
    //     tokenGetter:function tokenGetter(){
    //       return localStorage.getItem('access_token'); },
    //     whitelistedDomains : ['localhost:80'],
    //     blacklistedRoutes :['http://localhost/api/auth/login']
    //   }
    // }),
  ],
  declarations: [
    AppComponent,
    ...APP_CONTAINERS,
    P404Component,
    P500Component,
    LoginComponent,
    RegisterComponent,
    MyOrderComponent,
    MywalletComponent,
    MyaccountComponent,
    SecurityInfoComponent,
    ProfileComponent,
    MyorderhistoryComponent,
    OrderPrintComponent,
    AboutUsComponent,
    FaqComponent,
    PickupLocationsComponent,
    PrivacyPolicyComponent,
    TermsConditionsComponent,
    ContactUsComponent,
  ],
  entryComponents: [
		
	],
  providers: [{
    provide: LocationStrategy,
    useClass: HashLocationStrategy
  }  ,AuthGuard, LoggedGuard,LoggedChildGuard,
      AuthService,ErrorDialogService,
      { provide: HTTP_INTERCEPTORS,
        useClass: TokenInterceptor,
        multi: true
      },{ provide: HTTP_INTERCEPTORS,
         useClass: HttpErrorInterceptor,
          multi: true 
        }],
  bootstrap: [ AppComponent ]
})
export class AppModule { }
// required for AOT compilation
export function HttpLoaderFactory(http: HttpClient) {
  return new TranslateHttpLoader(http ,"./assets/i18n/", ".json");
}
