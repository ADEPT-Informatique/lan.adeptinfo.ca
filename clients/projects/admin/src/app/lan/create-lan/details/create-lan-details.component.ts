import { TimePickerConfig } from 'amazing-time-picker/src/app/atp-library/definitions';
import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {DateUtils} from '../../../utils/DateUtils';
import { AmazingTimePickerService } from 'amazing-time-picker';

@Component({
  selector: 'app-create-lan-details',
  templateUrl: './create-lan-details.component.html',
  styleUrls: ['./create-lan-details.component.css']
})
/**
 * Dialogue de création de LAN.
 */
export class CreateLanDetailsComponent {

  // Formulaire des détails du LAN
  detailsForm: FormGroup;

  // Configuration par défaut de l'interface pour sélectionner une heure
  timePickerConfig = <TimePickerConfig>{
    theme: 'material-purple',
    animation: 'rotate',
    changeToMinutes: true
  };

  constructor(
    private formBuilder: FormBuilder,
    private timePicker: AmazingTimePickerService
    ) {
    // Instantiation du formulaire
    this.detailsForm = this.formBuilder.group({
      name: ['LAN de l\'ADEPT - ', [Validators.required, Validators.max(255)]],
      price: ['0', Validators.required],
      playerCount: ['200', Validators.required],
      startDate: ['', Validators.required],
      startTime: ['', Validators.required],
      endDate: ['', Validators.required],
      endTime: ['', Validators.required],
      reservationDate: ['', Validators.required],
      reservationTime: ['', Validators.required],
      tournamentDate: ['', Validators.required],
      tournamentTime: ['', Validators.required],
    });
  }

  /**
   * Ouvrir l'interface pour sélectionner l'heure de début du LAN.
   */
  openStartTime(): void {
    const picker = this.timePicker.open(this.timePickerConfig);
    picker.afterClose().subscribe(time => {
      this.detailsForm.controls['startTime'].setValue(time);
      this.checkEndDateTime();
      this.checkReservationDateTime();
      this.checkTournamentDateTime();
    });
  }

  /**
   * Ouvrir l'interface pour sélectionner l'heure de fin du LAN.
   */
  openEndTime(): void {
    const picker = this.timePicker.open(this.timePickerConfig);
    picker.afterClose().subscribe(time => {
      this.detailsForm.controls['endTime'].setValue(time);
      this.checkEndDateTime();
    });
  }

  /**
   * Ouvrir l'interface pour sélectionner l'heure de réservation des tournois.
   */
  openReservationTime(): void {
    const picker = this.timePicker.open(this.timePickerConfig);
    picker.afterClose().subscribe(time => {
      this.detailsForm.controls['reservationTime'].setValue(time);
      this.checkReservationDateTime();
    });
  }

  /**
   * Ouvrir l'interface pour sélectionner l'heure d'inscription aux tournois.
   */
  openTournamentTime(): void {
    const picker = this.timePicker.open(this.timePickerConfig);
    picker.afterClose().subscribe(time => {
      this.detailsForm.controls['tournamentTime'].setValue(time);
      this.checkTournamentDateTime();
    });
  }

  /**
   * Obtenir l'erreur du nom du LAN.
   * @return string Texte de l'erreur
   */
  getNameError(): string {
    if (this.detailsForm.controls['name'].hasError('required')) {
      return 'Le nom du LAN est requis.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur du prix d'entrée du LAN.
   * @return string Texte de l'erreur
   */
  getPriceError(): string {
    if (this.detailsForm.controls['price'].hasError('required')) {
      return 'Le prix d\'entrée est requis.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur du nombre de joueurs dans le LAN.
   * @return string Texte de l'erreur
   */
  getPlayerCountError(): string {
    if (this.detailsForm.controls['playerCount'].hasError('required')) {
      return 'Le nombre de joueurs est requis.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de la date de début du LAN.
   * @return string Texte de l'erreur
   */
  getStartDateError(): string {
    if (this.detailsForm.controls['startDate'].hasError('required')) {
      return 'La date de début est requise.';
    } else if (this.detailsForm.controls['startDate'].hasError('lan_start_after_tournament')) {
      return 'La date de début doit être après l\'inscription aux tournois.';
    } else if (this.detailsForm.controls['startDate'].hasError('lan_start_after_reservation')) {
      return 'La date de début doit être après la date de réservation.';
    } else if (this.detailsForm.controls['startDate'].hasError('lan_end_after_lan_start')) {
      return 'La date de fin doit être après la date de début.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de l'heure de début du LAN.
   * @return string Texte de l'erreur
   */
  getStartTimeError(): string {
    if (this.detailsForm.controls['startTime'].hasError('required')) {
      return 'La date de début est requise.';
    } else if (this.detailsForm.controls['startTime'].hasError('lan_start_after_tournament')) {
      return 'La date de début doit être après l\'inscription aux tournois.';
    } else if (this.detailsForm.controls['startTime'].hasError('lan_start_after_reservation')) {
      return 'La date de début doit être après la date de réservation.';
    } else if (this.detailsForm.controls['startTime'].hasError('lan_end_after_lan_start')) {
      return 'La date de fin doit être après la date de début.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de la date de fin du LAN.
   * @return string Texte de l'erreur
   */
  getEndDateError(): string {
    if (this.detailsForm.controls['endDate'].hasError('required')) {
      return 'La date de fin est requise.';
    } else if (this.detailsForm.controls['endDate'].hasError('lan_end_after_lan_start')) {
      return 'La date de fin doit être après la date de début.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de l'heure de fin du LAN.
   * @return string Texte de l'erreur
   */
  getEndTimeError(): string {
    if (this.detailsForm.controls['endTime'].hasError('required')) {
      return 'L\'heure de fin est requise.';
    } else if (this.detailsForm.controls['endTime'].hasError('lan_end_after_lan_start')) {
      return 'La date de fin doit être après la date de début.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de la date de réservation.
   * @return string Texte de l'erreur
   */
  getReservationDateError(): string {
    if (this.detailsForm.controls['reservationDate'].hasError('required')) {
      return 'La date de reservation est requise.';
    } else if (this.detailsForm.controls['reservationDate'].hasError('lan_start_after_reservation')) {
      return 'La date de début doit être après la date de réservation.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de l'heure de réservation.
   * @return string Texte de l'erreur
   */
  getReservationTimeError(): string {
    if (this.detailsForm.controls['reservationTime'].hasError('required')) {
      return 'L\'heure de réservation est requise.';
    } else if (this.detailsForm.controls['reservationTime'].hasError('lan_start_after_reservation')) {
      return 'La date de début doit être après la date de réservation.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de la date de début du tournoi.
   * @return string Texte de l'erreur
   */
  getTournamentDateError(): string {
    if (this.detailsForm.controls['tournamentDate'].hasError('required')) {
      return 'La date des tournois est requise.';
    } else if (this.detailsForm.controls['tournamentDate'].hasError('lan_start_after_tournament')) {
      return 'La date de début doit être après l\'inscription aux tournois.';
    } else {
      return '';
    }
  }

  /**
   * Obtenir l'erreur de l'heure de début du tournoi.
   * @return string Texte de l'erreur
   */
  getTournamentTimeError(): string {
    if (this.detailsForm.controls['tournamentTime'].hasError('required')) {
      return 'L\'heure des tournois est requise.';
    } else if (this.detailsForm.controls['tournamentTime'].hasError('lan_start_after_tournament')) {
      return 'La date de début doit être après l\'inscription aux tournois.';
    } else {
      return '';
    }
  }

  /**
   * Valider les champs de date et de temps de la fin du LAN.
   */
  checkEndDateTime(): void {
    const dateTimes = this.getDateTimes();
    if (dateTimes.end != null && dateTimes.start != null && dateTimes.end < dateTimes.start) {
      this.setError(['endDate', 'endTime'], {'lan_end_after_lan_start': true});
    } else {
      this.setError(['reservationDate', 'reservationTime'], null);
    }
  }


  /**
   * Valider les champs de date et de temps du début de la réservation des places.
   */
  checkReservationDateTime(): void {
    const dateTimes = this.getDateTimes();
    if (dateTimes.reservation != null && dateTimes.start != null && dateTimes.reservation > dateTimes.start) {
      this.setError(['reservationDate', 'reservationTime'], {'lan_start_after_reservation': true});
    } else {
      this.setError(['reservationDate', 'reservationTime'], null);
    }
  }

  /**
   * Valider les champs de date et de temps du début de l'inscription aux tournois.
   */
  checkTournamentDateTime(): void {
    const dateTimes = this.getDateTimes();
    if (dateTimes.tournament != null && dateTimes.start != null && dateTimes.tournament > dateTimes.start) {
      this.setError(['tournamentDate', 'tournamentTime'], {'lan_start_after_tournament': true});
    } else {
      this.setError(['reservationDate', 'reservationTime'], null);
    }
  }

  /**
   * Activer une erreur sur un ou plusieurs champs.
   * @param fields Nom des champ du FormGroup de l'erreur
   * @param error Texte de l'erreur
   */
  setError(fields: string[], error: any): void {
    for (const field of fields) {
      this.detailsForm.controls[field].setErrors(error);
    }
  }

  getDateTimes(): { 'start', 'end', 'reservation', 'tournament' } {
    // Date et heure de début du LAN
    const start = DateUtils.getDateFromMomentAndString(
      this.detailsForm.controls['startDate'].value,
      this.detailsForm.controls['startTime'].value
    );

    // Date et heure de fin du LAN
    const end = DateUtils.getDateFromMomentAndString(
      this.detailsForm.controls['endDate'].value,
      this.detailsForm.controls['endTime'].value
    );

    // Date et heure de début de réservation des places
    const reservation = DateUtils.getDateFromMomentAndString(
      this.detailsForm.controls['reservationDate'].value,
      this.detailsForm.controls['reservationTime'].value
    );

    // Date et heure de début d'inscription aux tournois
    const tournament = DateUtils.getDateFromMomentAndString(
      this.detailsForm.controls['tournamentDate'].value,
      this.detailsForm.controls['tournamentTime'].value
    );

    return {
      'start': start,
      'end': end,
      'reservation': reservation,
      'tournament': tournament
    };
  }

}
