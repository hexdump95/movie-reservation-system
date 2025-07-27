import { Component, Inject } from '@angular/core';
import {
  MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from '@angular/material/dialog';
import {MatButton} from "@angular/material/button";
import {GetMovieDetailResponse} from "../../../core/movie-response";

@Component({
  selector: 'app-movie-dialog',
  templateUrl: './movie-dialog.component.html',
  standalone: true,
  imports: [
    MatButton,
    MatDialogActions,
    MatDialogContent,
    MatDialogTitle
  ],
  styleUrls: ['./movie-dialog.component.css']
})
export class MovieDialogComponent {
  constructor(
    public dialogRef: MatDialogRef<MovieDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public movie: GetMovieDetailResponse) {
  }

  onConfirm() {
    this.dialogRef.close(true);
  }
}
