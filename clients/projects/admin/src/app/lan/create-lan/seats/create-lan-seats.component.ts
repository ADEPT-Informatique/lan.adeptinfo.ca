import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { Chart, SeatService } from 'projects/core/src/public_api';

@Component({
  selector: 'app-create-lan-seats',
  templateUrl: './create-lan-seats.component.html',
  styleUrls: ['./create-lan-seats.component.css']
})
/**
 * Dialogue d'entrée de l'événement seats.io seats.io
 */
export class CreateLanSeatsComponent implements OnInit {

  // Formulaire des places
  seatsForm: FormGroup;

  // Cartes de seats.io disponibles
  charts: Chart[];

  // Carte de seats.io sélectionnée
  selectedChart: Chart;

  // Options du caroussel qui affiche les cartes de seats.io
  carouselOptions = {
    items: 1,
    dots: true,
    center: true,
    nav: true,
    loop: true,
    responsiveClass: true,
    responsive: {
      // Si l'écran fait plus de 600px de largeur, afficher 2 items
      600: {
        items: 2
      }
    }
  };

  constructor(private formBuilder: FormBuilder, private seatService: SeatService) {
    // Instantiation du formulaire
    this.seatsForm = this.formBuilder.group({
      eventKey: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    // Obtenir cartes seats.io
    this.seatService.getSeatsioEvents()
      .subscribe(charts => {
        // Filtrer pour n'obtenir que celles qui sont publiées (qui ont des événements)
        this.charts = charts.items.filter(function (item) {
          return item.status === 'PUBLISHED_WITH_DRAFT' ||
            item.status === 'PUBLISHED';
        });
      });
  }

  /**
   * Sélectionner une carte pour qu'elle devienne la carte courante.
   * @param chart Carte à rendre courante
   */
  selectChart(chart: any): void {
    this.selectedChart = chart;
  }

  /**
   * Désélectionner la carte courante.
   */
  unselectChart(): void {
    this.selectedChart = null;
  }

  /**
   * Obtenir l'erreur de la clé d'événement.
   * @return string Texte de l'erreur
   */
  getEventKeyError(): string {
    if (this.seatsForm.controls['eventKey'].hasError('required')) {
      return 'La clé d\'événement est requise.';
    } else {
      return '';
    }
  }
}
