import {Component, Inject} from '@angular/core';
import {
  MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from "@angular/material/dialog";
import {TheaterService} from "../../../core/theater.service";
import {TheaterDetailResponse} from "../../../core/theater-response";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {MatButton} from "@angular/material/button";
import {LoaderComponent} from "../../../shared/loader/loader.component";
import {NgStyle} from "@angular/common";

@Component({
  selector: 'app-theater-detail-dialog',
  standalone: true,
  imports: [
    FormsModule,
    MatButton,
    MatDialogActions,
    MatDialogContent,
    MatDialogTitle,
    ReactiveFormsModule,
    LoaderComponent,
    NgStyle
  ],
  templateUrl: './theater-detail-dialog.component.html',
  styleUrl: './theater-detail-dialog.component.css'
})
export class TheaterDetailDialogComponent {
  theater!: TheaterDetailResponse;
  seatLength: number = 0;

  constructor(
    public dialogRef: MatDialogRef<TheaterDetailDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public theaterId: number,
    private theaterService: TheaterService
  ) {
  }

  ngOnInit() {
    this.theaterService.getTheater(this.theaterId)
      .subscribe(res => {
        this.theater = res;
        this.seatLength = (window.innerWidth - 160) / this.theater.seats[0].length;
      });
  }

  onAccept() {
    this.dialogRef.close();
  }
}
