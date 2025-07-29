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
    @Inject(MAT_DIALOG_DATA) public showtimes: GetShowtimeResponse[],
    private dialog: MatDialog,
    private movieService: MovieService,
  ) {
  }

  onConfirm() {
    this.dialogRef.close(true);
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
                this.showtimes = this.showtimes.filter((x: GetShowtimeResponse) => x.id !== showtime.id);
            });
        }
      }
    );
  }

}
