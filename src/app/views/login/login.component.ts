import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  loginForm: FormGroup;
  isLoading:boolean=false;
  submitted: boolean=false;
  errMsg: string='';

  constructor(private authService: AuthService, private formBuilder: FormBuilder, private router: Router) { }

  ngOnInit() {
    this.loginForm = this.formBuilder.group({
      uName: ['', [Validators.required,Validators.pattern('^[A-Za-z0-9_-]{6,20}$')]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  get f() { return this.loginForm.controls; }

  errorRest(){
    this.errMsg='';
  }

  login() {
    this.submitted=true;
    this.isLoading=true;
    if (this.loginForm.invalid) { this.isLoading = false; return;}
    this.authService.login(
      {username: this.f.uName.value,
        password: this.f.password.value
      }
    )
    .subscribe(data => {
      this.isLoading=false;
      if (data) { // login Done
        this.router.navigate(['/']);
      }
      else{
        this.errMsg='Invalid User ID Or Password, Try Again!';
      }
    },
    error => {
      this.isLoading=false;
      this.errMsg='Server Error While Try Again Later!';
      console.log(error);
    },
    () => {// 'onCompleted' callback.// No errors, route to new page here
    }
    );
  }
}
