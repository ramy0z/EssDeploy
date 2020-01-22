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
  isLoading:Boolean=false;

  constructor(private authService: AuthService, private formBuilder: FormBuilder, private router: Router) { }

  ngOnInit() {
    this.loginForm = this.formBuilder.group({
      username: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  get f() { return this.loginForm.controls; }


  login() {
    this.isLoading=true;
    this.authService.login(
      {username: this.f.username.value,
        password: this.f.password.value
      }
    )
    .subscribe(success => {
      if (success) {
        this.isLoading=false;
        this.router.navigate(['/']);
      }
    },
    error => {
      console.log(error);
    },
    () => {// 'onCompleted' callback.// No errors, route to new page here
    }
    );
  }
}
