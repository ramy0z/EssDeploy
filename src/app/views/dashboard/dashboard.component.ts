import { Component, OnInit } from '@angular/core';
import { getStyle, hexToRgba } from '@coreui/coreui/dist/js/coreui-utilities';
import { CustomTooltips } from '@coreui/coreui-plugin-chartjs-custom-tooltips';
import { TranslateService } from '@ngx-translate/core';

@Component({
  templateUrl: 'dashboard.component.html'
})

export class DashboardComponent {

  constructor( private translate: TranslateService) { 
    translate.setDefaultLang('ar');
  }
  // useLanguage(language: string) {
  //   this.translate.use(language);
  // }
}
