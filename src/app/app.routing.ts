import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

// Import Containers
import { DefaultLayoutComponent } from './containers';

import { P404Component } from './views/error/404.component';
import { P500Component } from './views/error/500.component';
import { LoginComponent } from './views/login/login.component';
import { RegisterComponent } from './views/register/register.component';
import { AuthGuard } from './guards/auth.guard';
import { MyOrderComponent } from './views/orders/myorder.component';
import { MywalletComponent } from './views/accounts/mywallet/mywallet.component';
import { MyaccountComponent } from './views/accounts/myaccount/myaccount.component';
import { ProfileComponent } from './views/accountSetting/profile/profile.component';
import { SecurityInfoComponent } from './views/accountSetting/security-info/security-info.component';
import { MyorderhistoryComponent } from './views/orders/myorderhistory/myorderhistory.component';
import { OrderPrintComponent } from './views/orders/order-print/order-print.component';
import { AboutUsComponent } from './views/pages/about-us/about-us.component';
import { ContactUsComponent } from './views/pages/contact-us/contact-us.component';
import { FaqComponent } from './views/pages/faq/faq.component';
import { PickupLocationsComponent } from './views/pages/pickup-locations/pickup-locations.component';
import { PrivacyPolicyComponent } from './views/pages/privacy-policy/privacy-policy.component';
import { TermsConditionsComponent } from './views/pages/terms-conditions/terms-conditions.component';

export const routes: Routes = [
  {path: '',redirectTo: 'dashboard',pathMatch: 'full',},
  {path: '404',component: P404Component,data: {title: 'Page 404'}},
  { path: '500',component: P500Component,data: {title: 'Page 500'}},
  {path: 'login',component: LoginComponent,canActivate: [AuthGuard],data: {title:  'الدخول'}},
  { path: 'register',component: RegisterComponent,data: {title: 'تسجيل الدخول'}},
  {path: '',component: DefaultLayoutComponent,data: {title: 'الصفحة الرئيسية'},
    children: [
      { path: 'orderprint',component: OrderPrintComponent,data: {title: 'طلب طباعة'}} ,
      { path: 'myorder',component: MyOrderComponent,data: {title: 'طلباتى'}} ,
      { path: 'orderHistory',component: MyorderhistoryComponent,data: {title: 'تقرير طلباتى'}} ,
      { path: 'mywallet',component: MywalletComponent,data: {title: 'محفظتى'}} ,
      { path: 'myaccount',component: MyaccountComponent,data: {title: 'حركة الحساب'}} ,
      { path: 'profile',component: ProfileComponent,data: {title: 'الصفحة الشخصية'}} ,
      { path: 'security-info',component: SecurityInfoComponent,data: {title: 'الحماية'}},
      { path: 'about-us',component: AboutUsComponent,data: { title: 'من نحن' }},
      { path: 'contact-us',component: ContactUsComponent,data: { title: 'تواصل معنا' }},
      { path: 'faq',component: FaqComponent,data: { title: 'الأسئلة الشائعة' }},
      { path: 'pickup-locations',component: PickupLocationsComponent,data: { title: 'نقاط الإستلام' }},
      { path: 'privacy-policy',component: PrivacyPolicyComponent,data: { title: 'سياسةالخصوصية' }},
      { path: 'terms-conditions',component: TermsConditionsComponent,data: { title: 'الشروط والاحكام' }}
      ,{
        path: 'dashboard',
        loadChildren: () => import('./views/dashboard/dashboard.module').then(m => m.DashboardModule)
      }
    ]
  },
  { path: '**', component: P404Component }
];

@NgModule({
  imports: [ RouterModule.forRoot(routes) ],
  exports: [ RouterModule ]
})
export class AppRoutingModule {}
