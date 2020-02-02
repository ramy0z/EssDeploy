import { Component, OnInit } from '@angular/core';
import { FileUploader } from 'ng2-file-upload';
import { Ng2ImgMaxService } from 'ng2-img-max';
import { UserService } from '../../../services/user.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';


@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.scss']
})
export class ProfileComponent implements OnInit {
  profileForm: FormGroup;
  errormessage: string = '';
  viewLoading: boolean = false;
  input_image:string;
  maxDt = new Date();
  picker1;
  country;
  state;
  city;
  private token = localStorage.getItem('JWT_TOKEN');
  // url=this.ser.upload_file();
  public uploader: FileUploader = new FileUploader({ url: '', authToken: this.token, itemAlias: 'upload' });
  public imageprivew: any;
  constructor( private formBuilder: FormBuilder,private _userService :UserService ,private _ng2ImgMax: Ng2ImgMaxService) { }

  ngOnInit() {
    this.profileForm = this.formBuilder.group({
      fullName: ['', [Validators.required,Validators.pattern('^(?![0-9]+$)[A-Za-z0-9_-]{4,90}$')]],
      gender:   ['', Validators.required],
      phone:    ['', [Validators.required,Validators.pattern('[0-9]+'), 
                      Validators.min(9999999),Validators.max(99999999999999999999)]],
      birthDate:['', Validators.required],
      userContry:['', Validators.required],
      userState:['', Validators.required],
      userCity: ['', Validators.required],
      userStreet: [],
    });

    this.getPlace('country');
    this.imageprivew = '';
    //upload Image   
    this.uploader.onAfterAddingFile = (file) => { file.withCredentials = false; };
  }

  getPlace( type,id=''){
    this._userService.getPlaceSrv(type,id).subscribe(
      res => {
        if (res['response']['status']==200) {
          //console.log(res);
          let data=res['response']['data']
          if(type=='country'){
            this.country=data;
          }
          else if(type=='state'){
            this.state=data;
          }
          else if(type=='city'){
            this.city=data;
          }
         //return res['response']['data'];
        }
      },
      err =>{console.log("Error : "+type,err);return[]; },
      ()=>{console.log("Finish Place : " + type)}
    );
  }
  
  get f() { return this.profileForm.controls; }

  onCountryChange(userContryId){
    //console.log(userContry);
    if(userContryId){
      this.getPlace('state',userContryId);
    }
  }
  onStateChange(userStateId){
    //console.log(userState);
    if(userStateId){
      this.getPlace('city',userStateId);
    }
  }

  handleUpload(event: any) {
    if (event.target.files && event.target.files[0]) {
      this.viewLoading = true;
      var reader = new FileReader();
      reader.onload = (event: ProgressEvent) => { this.imageprivew = (<FileReader>event.target).result; }
      reader.readAsDataURL(event.target.files[0]);

      this._ng2ImgMax.resizeImage(event.target.files[0], 600, 600).subscribe(
        result => {
          const newImage = new File([result], result.name);
          this.uploader.clearQueue();
          this.uploader.addToQueue([newImage]);
          //this.uploader.uploadAll();
          //console.log(result);
          reader.onload = (event: ProgressEvent) => { this.imageprivew = (<FileReader>event.target).result; }
          reader.readAsDataURL(event.target.files[0]);
          this.viewLoading = false;
        },
        error => console.log(error)
      );
    }

  }
  savePersonalInfo() {
    // const _title: string = 'Workout';
    // const _description: string = 'Are you sure to Edit this Workout?';
    // const _waitDesciption: string = 'Workout is Editing...';
    // const dialogRef = this.layoutUtilsService.RenwElement(_title, _description, _waitDesciption);
    // dialogRef.afterClosed().subscribe(res => {
    //   if (!res) { return; }
    //   else {
    //     //there are change 
    //     const id = this.workout['id'];
    //     //edit and update image 
    //     if (this.workout.category != this.data['rowData']['category']) workoutObj['category'] = this.workout.category;
    //     if (this.workout.name != this.data['rowData']['name']) workoutObj['name'] = this.workout.name;
    //     if (this.workout.description != this.data['rowData']['description']) workoutObj['description'] = this.workout.description;
    //     if (this.workout.video_url != this.data['rowData']['video_url']) workoutObj['video_url'] = this.convert_youtube(this.workout.video_url);
    //     if (input_image != '') {
    //       // workoutObj['old_image'] = this.workout['image_url']; //to delete old image then add new one
    //       this.uploader.onBuildItemForm = (fileItem: any, form: any) => {
    //         form.append('old_image', this.workout['image_url']);
    //       };
    //       //add new image
    //       this.uploader.uploadAll();
    //       this.uploader.onCompleteItem = (item: any, response: any, status: any, headers: any) => {
    //         var responsePath = JSON.parse(response);
    //         if (responsePath.result) {
    //           // this.workout.image_url = responsePath.image;
    //           workoutObj['image_url'] = responsePath.data;
    //           this.editWorkoutDataToDB(workoutObj, id);
    //         }
    //         else { this.errormessage = "Error While Upload Image"; }
    //       };
    //     }
    //     else {
    //       //edit and not update image
    //       this.editWorkoutDataToDB(workoutObj, id);
    //     }
    //   }
    // });
  }

}
