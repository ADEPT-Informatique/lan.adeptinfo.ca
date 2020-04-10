import {Component, ViewChild} from '@angular/core';
import {FormBuilder} from '@angular/forms';
import {MediaMatcher} from '@angular/cdk/layout';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';
import {CreateLanSeatsComponent} from './seats/create-lan-seats.component';
import {CreateLanCoordinatesComponent} from './coordinates/create-lan-coordinates.component';
import {CreateLanRulesComponent} from './rules/create-lan-rules.component';
import {CreateLanDescriptionComponent} from './description/create-lan-description.component';
import {Errors, Lan, LanService} from 'core';
import {DateUtils} from '../../utils/DateUtils';
import {SwalComponent} from '@sweetalert2/ngx-sweetalert2';
import {Router} from '@angular/router';

@Component({
  selector: 'app-create-lan',
  templateUrl: './create-lan.component.html',
  styleUrls: ['./create-lan.component.css']
})
/**
 * Dialogue de création de LAN.
 */
export class CreateLanComponent {

  // Les communications avec le serveur sont en cours, l'interface est donc désactivée pendant ce temps
  isCreatingLan = false;

  // Formulaire des détails du LAN
  @ViewChild(CreateLanDetailsComponent) createLanDetailsComponent: CreateLanDetailsComponent;

  // Formulaire de seats.io
  @ViewChild(CreateLanSeatsComponent) createLanSeatsComponent: CreateLanSeatsComponent;

  // Formulaire des coordonnées
  @ViewChild(CreateLanCoordinatesComponent) createLanCoordinatesComponent: CreateLanCoordinatesComponent;

  // Formulaire des règlements
  @ViewChild(CreateLanRulesComponent) createLanRulesComponent: CreateLanRulesComponent;

  // Formulaire de description
  @ViewChild(CreateLanDescriptionComponent) createLanDescriptionComponent: CreateLanDescriptionComponent;

  // Surveille la largeur courante de l'écran de l'utilisateur
  mobileQuery: MediaQueryList;

  // Erreurs qui peuvent être retournées par l'API
  errors: Errors;

  // Index courant du stepper
  currentIndex = 0;

  // Fenêtre qui confirme à l'utilisateur que le LAN a bien été créé
  @ViewChild('createLanSuccessSwal') private createLanSuccessSwal: SwalComponent;

  constructor(
    private formBuilder: FormBuilder,
    private media: MediaMatcher,
    private lanService: LanService,
    private router: Router
  ) {
    // Le changement de mobile à plein écran s'effectue lorsque l'écran fait 960 pixels de large
    this.mobileQuery = this.media.matchMedia('(min-width: 960px)');
  }

  get coordinatesLongitude() {
    return this.createLanCoordinatesComponent ? this.createLanCoordinatesComponent.longitude : null;
  }

  get detailsForm() {
    return this.createLanDetailsComponent ? this.createLanDetailsComponent.detailsForm : null;
  }

  get seatsForm() {
    return this.createLanSeatsComponent ? this.createLanSeatsComponent.seatsForm : null;
  }

  get coordinatesLatitude() {
    return this.createLanCoordinatesComponent ? this.createLanCoordinatesComponent.latitude : null;
  }

  get coordinatesForm() {
    return this.createLanCoordinatesComponent ? this.createLanCoordinatesComponent.coordinatesForm : null;
  }

  /**
   * Créer un LAN avec les champs qui ont été remplis.
   */
  createLan(): void {

    const lan: Lan = new Lan(
      this.detailsForm.controls['name'].value,
      DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['startDate'].value,
        this.detailsForm.controls['startTime'].value
      ),
      DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['endDate'].value,
        this.detailsForm.controls['endTime'].value
      ),
      DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['reservationDate'].value,
        this.detailsForm.controls['reservationTime'].value
      ),
      DateUtils.getDateFromMomentAndString(
        this.detailsForm.controls['tournamentDate'].value,
        this.detailsForm.controls['tournamentTime'].value
      ),
      this.detailsForm.controls['playerCount'].value,
      this.detailsForm.controls['price'].value,
      this.seatsForm.controls['eventKey'].value,
      this.coordinatesLatitude,
      this.coordinatesLongitude,
      this.rulesForm.controls['rules'].value,
      this.descriptionForm.controls['description'].value,
    );

    this.lanService.createLan(lan).subscribe(
      (data: Lan) => {
        this.errors = null;
        this.isCreatingLan = false;
        // this.createLanSuccessSwal.show().then(() => this.router.navigateByUrl('/'));
      },
      (err: Errors) => {
        this.errors = err;
        this.isCreatingLan = false;
      }
    );
  }

  get rulesForm() {
    return this.createLanRulesComponent ? this.createLanRulesComponent.rulesForm : null;
  }

  get descriptionForm() {
    return this.createLanDescriptionComponent ? this.createLanDescriptionComponent.descriptionForm : null;
  }
}
