import {Component, OnInit} from '@angular/core';
import { environment } from 'projects/user/src/environments/environment';

@Component({
  selector: 'app-layout-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.css']
})
/**
 * Bas de page du client.
 */
export class FooterComponent implements OnInit {

  version: string;
  currentDate: Date;

  ngOnInit(): void {

    // Obtenir la version courante du site
    this.version = environment.version;

    // Obtenir la date courante
    this.currentDate = new Date();

  }

}
