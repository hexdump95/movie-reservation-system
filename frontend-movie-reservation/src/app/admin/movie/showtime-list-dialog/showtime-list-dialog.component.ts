import {Component, Inject} from '@angular/core';
import {MatButton} from "@angular/material/button";
import {
  MAT_DIALOG_DATA,
  MatDialog,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from "@angular/material/dialog";
import {ReactiveFormsModule} from "@angular/forms";
import {GetShowtimeResponse} from "../../../core/movie-response";
import {ConfirmDialogComponent} from "../../../shared/confirm-dialog/confirm-dialog.component";
import {MovieService} from "../../../core/movie.service";
import {DatePipe} from "@angular/common";
import {AddShowtimeDialogComponent} from "./add-showtime-dialog/add-showtime-dialog.component";

@Component({
  selector: 'app-showtime-list-dialog',
  standalone: true,
  imports: [
    MatButton,
    MatDialogActions,
    MatDialogContent,
    MatDialogTitle,
    ReactiveFormsModule,
    DatePipe
  ],
  templateUrl: './showtime-list-dialog.component.html',
  styleUrl: './showtime-list-dialog.component.css'
})
export class ShowtimeListDialogComponent {
  constructor(
    public dialogRef: MatDialogRef<ShowtimeListDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any,
    private dialog: MatDialog,
    private movieService: MovieService,
  ) {
  }

  onConfirm() {
    this.dialogRef.close(true);
  }

  openAddShowtimeDialog(): void {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    const dialogRef = this.dialog.open(AddShowtimeDialogComponent, {
      data: this.data.movieId,
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(res => {
      if (res) {
        this.data.showtimes.push(res);
      }
    });

  }

  openDeleteDialog(showtime: GetShowtimeResponse): void {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      data: {
        title: `Delete Showtime '${showtime.id}'`,
        message: 'Are you sure you want to delete this showtime?'
      },
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(res => {
        if (res) {
          this.movieService.removeShowtime(showtime.id)
            .subscribe(r => {
              if (r.success)
                this.data.showtimes = this.data.showtimes.filter((x: GetShowtimeResponse) => x.id !== showtime.id);
            });
        }
      }
    );
  }

}
