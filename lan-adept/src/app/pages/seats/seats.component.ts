import { Component, OnInit } from '@angular/core';
import { UserService } from '../../core/services/user.service';


@Component({
  selector: 'app-seats',
  templateUrl: './seats.component.html',
  styleUrls: ['./seats.component.scss']
})
export class SeatsComponent implements OnInit {

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

  constructor(public userService:UserService) { }


  ngOnInit(): void {
  }

}
