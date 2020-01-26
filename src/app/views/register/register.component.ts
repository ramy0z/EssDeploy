import { Component } from '@angular/core';
import { FormGroup, Validators , FormBuilder}   from '@angular/forms';
import { Router } from '@angular/router';
import { UserService } from '../../services/user.service';
// import custom validator to validate that password and confirm password fields match

@Component({
  selector: 'app-dashboard',
  templateUrl: 'register.component.html'
})
export class RegisterComponent {
  registerForm: FormGroup;
  submitted = false; 
  isLoading = false; 
  error:boolean=false;
  error_message: string;
  constructor( private UserService:UserService , private router:Router, private formBuilder: FormBuilder) { }

  ngOnInit() {
      this.registerForm = this.formBuilder.group({
          uName: ['', [Validators.required,Validators.pattern('^(?![0-9]+$)[A-Za-z0-9_-]{6,20}$')]],
          phone: ['', [Validators.required,Validators.pattern('[0-9]+'), 
                          Validators.min(9999999),Validators.max(99999999999999999999)]],
          email: ['', [Validators.required,Validators.email]],
          password: ['', [Validators.required, Validators.minLength(8)]],
          confirmPass: ['', Validators.required]
      },{validator: MustMatch('password', 'confirmPass')});
  }

  // convenience getter for easy access to form fields
  get f() { return this.registerForm.controls; }

  onSubmit() {
    this.isLoading=true;
      this.submitted = true;
      // stop here if form is invalid
      if (this.registerForm.invalid) { this.isLoading=false; return;}
     // alert('SUCCESS!! :-)\n\n' + JSON.stringify(this.registerForm.value))
     //alert(this.registerForm.value)
      let udata= this.registerForm.value;
      delete udata["confirmPass"];
     // console.log(udata);
      this.UserService.signUp(udata).subscribe(req=>{
        console.log(req);
        alert(req);
      },
      error=>{
        this.error=!this.error;
        this.error_message="Your Credentials Do not Match";
        console.log(this.error_message);
      },
      ()=>{this.isLoading=false;
        this.submitted = false;
      }
    );
  }


}

// custom validator to check that two fields match
export function MustMatch(controlName: string, matchingControlName: string) {
  return (formGroup: FormGroup) => {
      const control = formGroup.controls[controlName];
      const matchingControl = formGroup.controls[matchingControlName];

      if (matchingControl.errors && !matchingControl.errors.mustMatch) {
          // return if another validator has already found an error on the matchingControl
          return;
      }

      // set error on matchingControl if validation fails
      if (control.value !== matchingControl.value) {
          matchingControl.setErrors({ mustMatch: true });
      } else {
          matchingControl.setErrors(null);
      }
  }
}