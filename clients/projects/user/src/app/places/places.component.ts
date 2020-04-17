import { Component, OnInit } from '@angular/core';
import { UserService } from 'projects/core/src/public_api';
import { Router } from '@angular/router';
import { ApiService } from 'projects/core/src/lib/services/api.service';
import { NgForm, Form, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-places',
  templateUrl: './places.component.html',
  styleUrls: ['./places.component.css']
})
export class PlacesComponent {
  //TODO
  //Faire un get a l'api pour obtenir le event key a partir de l'api
  config = {
    publicKey: "19aa9acc-c576-465e-bcbf-28738cb997a4",
    event: "a2e58f40-980f-4b4f-9c1c-ec7098727e7a",
    fitTo: 'width',
    style: { font: 'Roboto', border: 'max', padding: 'spacious' },
    features: {
      disabled: ['booths', 'tables']
    },
    maxSelectedObjects: 1,
    selectedObjectsInputName: 'selectedSeat'
  }

  constructor(private formBuilder: FormBuilder, private userService: UserService, private router: Router, private apiService: ApiService, private toastr: ToastrService) {

  }

  onSubmit(event: any) {
    console.log(event.target.seat == null)
    if (!this.userService.isConnected()) {
      this.toastr.warning("Veulliez vous connectez afin de reserver une place.", "Non connecté.", {
        tapToDismiss: true,
        timeOut: 3000,
        positionClass: "toast-center-center"

      })
      return;
    }
    else if (event.target.seat == null) {
      this.toastr.warning("Veulliez selectionnez une place.", "", {
        tapToDismiss: true,
        timeOut: 3000,
        positionClass: "toast-center-center"

      })
      return;
    }
    else {
      return this.apiService.post('/seat/book/' + event.target.selectedSeat.value, 1).subscribe(
        () => { this.router.navigate(["/Home"]); },
        (error) => { console.log(error); }
      )
    }
  }

}
