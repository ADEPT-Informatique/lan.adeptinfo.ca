import {AfterViewInit, Component, ViewChild} from '@angular/core';
import {FormBuilder, FormGroup} from '@angular/forms';
import {TdTextEditorComponent} from '@covalent/text-editor';

@Component({
  selector: 'app-create-lan-rules',
  templateUrl: './create-lan-rules.component.html',
  styleUrls: ['./create-lan-rules.component.css']
})
/**
 * Dialogue pour entrer les règles du LAN.
 */
export class CreateLanRulesComponent implements AfterViewInit {

  // Formulaire des règles du LAN
  rulesForm: FormGroup;
  @ViewChild('textEditor') private textEditor: TdTextEditorComponent;

  constructor(
    private formBuilder: FormBuilder
  ) {
    // Instantiation du formulaire
    this.rulesForm = this.formBuilder.group({
      rules: []
    });
  }

  ngAfterViewInit(): void {
    // Avant de changer le texte
    this.textEditor.easyMDE.codemirror.on('beforeChange', (instance: any, changeObj: any) => {
      // Si le texte va être plus long que 20 000 caractère
      if (this.textEditor.easyMDE.value().length >= 20000 && changeObj.origin === '+input') {
        // Annuler l'édition
        changeObj.cancel();
      }
    });
  }
}
