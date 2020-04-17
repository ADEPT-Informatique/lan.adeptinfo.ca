import { AmazingTimePickerModule } from 'amazing-time-picker';
import {CommonModule} from '@angular/common';
import {NgModule} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {HttpClientModule} from '@angular/common/http';
import {RouterModule} from '@angular/router';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {FlexLayoutModule} from '@angular/flex-layout';
import {LayoutModule} from '@angular/cdk/layout';
import {
  MatButtonModule,
  MatCardModule,
  MatCheckboxModule,
  MatDatepickerModule,
  MatDialogModule,
  MatDividerModule,
  MatExpansionModule,
  MatFormFieldModule,
  MatIconModule,
  MatInputModule,
  MatListModule,
  MatMenuModule,
  MatProgressSpinnerModule,
  MatSelectModule,
  MatSidenavModule,
  MatStepperModule,
  MatToolbarModule,
  MatTooltipModule
} from '@angular/material';
import {ShowAuthedDirective} from './show-authed.directive';
import {HasPermissionPipe} from './has-permission.pipe';
import {MatMomentDateModule} from '@angular/material-moment-adapter';
import {SweetAlert2Module} from '@sweetalert2/ngx-sweetalert2';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    MatButtonModule,
    MatCardModule,
    MatCheckboxModule,
    MatDatepickerModule,
    MatDialogModule,
    MatDividerModule,
    MatExpansionModule,
    MatFormFieldModule,
    MatIconModule,
    MatInputModule,
    MatListModule,
    MatMenuModule,
    MatProgressSpinnerModule,
    MatSelectModule,
    MatSidenavModule,
    MatStepperModule,
    MatToolbarModule,
    MatTooltipModule,
    RouterModule,
    SweetAlert2Module.forRoot()
  ],
  declarations: [
    ShowAuthedDirective,
    HasPermissionPipe
  ],
  exports: [
    CommonModule,
    ShowAuthedDirective,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    HasPermissionPipe,
    AmazingTimePickerModule,
    RouterModule,
    BrowserAnimationsModule,
    FlexLayoutModule,
    LayoutModule,
    MatToolbarModule,
    MatSidenavModule,
    MatListModule,
    MatIconModule,
    MatButtonModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatDividerModule,
    MatCheckboxModule,
    MatMenuModule,
    MatSelectModule,
    MatTooltipModule,
    MatDialogModule,
    MatStepperModule,
    MatDatepickerModule,
    MatMomentDateModule,
    MatProgressSpinnerModule,
    MatExpansionModule,
    SweetAlert2Module
  ]
})
export class SharedModule {
}
