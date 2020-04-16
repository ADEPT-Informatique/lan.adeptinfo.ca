import {NgModule} from '@angular/core';
import {SharedModule} from '../../shared/shared.module';
import {CreateLanComponent} from './create-lan.component';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';
import {CreateLanSeatsComponent} from './seats/create-lan-seats.component';
import {SeatsioAngularModule} from '@seatsio/seatsio-angular';
import {OwlModule} from 'ngx-owl-carousel';
import {CreateLanCoordinatesComponent} from './coordinates/create-lan-coordinates.component';
import {AgmCoreModule} from '@agm/core';
import {CreateLanRulesComponent} from './rules/create-lan-rules.component';
import {CreateLanDescriptionComponent} from './description/create-lan-description.component';
import {CovalentTextEditorModule} from '@covalent/text-editor';
import { environment } from 'projects/user/src/environments/environment';

@NgModule({
  imports: [
    SharedModule,
    SeatsioAngularModule,
    OwlModule,
    AgmCoreModule.forRoot({
      apiKey: environment.googleMapsApiKey,
      libraries: ['places']
    }),
    CovalentTextEditorModule
  ],
  declarations: [
    CreateLanComponent,
    CreateLanDetailsComponent,
    CreateLanSeatsComponent,
    CreateLanCoordinatesComponent,
    CreateLanRulesComponent,
    CreateLanDescriptionComponent
  ]
})
export class CreateLanModule {
}
