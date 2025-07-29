import {Component} from '@angular/core';
import {MovieService} from "../../core/movie.service";
import {MatButton} from "@angular/material/button";
import {MatDialog} from "@angular/material/dialog";
import {MovieDialogComponent} from "./movie-dialog/movie-dialog.component";
import {LoaderComponent} from "../../shared/loader/loader.component";
import {ConfirmDialogComponent} from "../../shared/confirm-dialog/confirm-dialog.component";
import {GetMovieResponse, UpdateMovieResponse} from "../../core/movie-response";
import {EditMovieDialogComponent} from "./edit-movie-dialog/edit-movie-dialog.component";
import {CreateMovieDialogComponent} from "./create-movie-dialog/create-movie-dialog.component";
import {ShowtimeListDialogComponent} from "./showtime-list-dialog/showtime-list-dialog.component";

@Component({
  selector: 'app-movie',
  standalone: true,
  templateUrl: './movie.component.html',
  imports: [
    MatButton,
    LoaderComponent
  ],
  styleUrl: './movie.component.css'
})
export class MovieComponent {
  movies!: GetMovieResponse[];
  loading = true;

  constructor(private movieService: MovieService,
              private dialog: MatDialog,) {
  }

  ngOnInit() {
    this.movieService.getMovies().subscribe(
      (res: GetMovieResponse[]) => {
        this.movies = res;
        this.loading = false;
      }
    )
  }

  openShowtimesDialog(movieId: number) {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    this.loading = true;
    this.movieService.getShowtimesByMovieId(movieId).subscribe(showtimes => {
      this.loading = false;
      this.dialog.open(ShowtimeListDialogComponent, {
        data: showtimes,
        disableClose: true
      });
    });
  }


  openMovieDialog(movieId: number) {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    this.loading = true;
    this.movieService.getMovie(movieId).subscribe(movie => {
      this.loading = false;
      this.dialog.open(MovieDialogComponent, {
        data: movie,
        disableClose: true
      });
    });
  }

  openCreateMovieDialog() {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    this.loading = true;
    this.loading = false;
    const dialogRef = this.dialog.open(CreateMovieDialogComponent,
      {
        disableClose: true
      });
    dialogRef.afterClosed().subscribe(res => {
      if (res) {
        window.location.reload();
      }
    });
  }

  openEditMovieDialog(movieId: number) {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    this.loading = true;
    this.movieService.getMovie(movieId).subscribe(movie => {
      this.loading = false;
      const dialogRef = this.dialog.open(EditMovieDialogComponent, {
        data: {movieId, movie},
        disableClose: true
      });
      dialogRef.afterClosed().subscribe((res: UpdateMovieResponse|null) => {
        if(res) {
          const index = this.movies.findIndex(x => x.id === res.id);
          if (index !== -1) {
            this.movies[index] = {id: res.id, title: res.title, year: res.year, genreName: res.genreName};
          }
        }
      });
    });
  }

  openDeleteDialog(movie: GetMovieResponse): void {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      data: {
        title: `Delete Movie '${movie.title}'`,
        message: 'Are you sure you want to delete this movie?'
      },
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(res => {
        if (res) {
          this.movieService.deleteMovie(movie.id)
            .subscribe(_ => {
              this.movies = this.movies.filter((x: GetMovieResponse) => x.id !== movie.id);
            });
        }
      }
    );
  }

}
