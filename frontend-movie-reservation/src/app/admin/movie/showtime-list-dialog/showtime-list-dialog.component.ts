import {Component, Inject} from '@angular/core';
import {MatButton} from "@angular/material/button";
import {
  MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from "@angular/material/dialog";
import {ReactiveFormsModule} from "@angular/forms";
import { GetShowtimeResponse} from "../../../core/movie-response";

@Component({
  selector: 'app-showtime-list-dialog',
  standalone: true,
  imports: [
    MatButton,
    MatDialogActions,
    MatDialogContent,
    MatDialogTitle,
    ReactiveFormsModule
  ],
  templateUrl: './showtime-list-dialog.component.html',
  styleUrl: './showtime-list-dialog.component.css'
})
export class ShowtimeListDialogComponent {
  constructor(
    public dialogRef: MatDialogRef<ShowtimeListDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public showtimes: GetShowtimeResponse[]) {
  }

  onConfirm() {
    this.dialogRef.close(true);
  }
}
