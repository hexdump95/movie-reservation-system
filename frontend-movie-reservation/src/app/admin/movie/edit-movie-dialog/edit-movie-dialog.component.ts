import {Component, Inject} from '@angular/core';
import {
  MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from "@angular/material/dialog";
import {MatButton} from "@angular/material/button";
import {FormBuilder, FormGroup, ReactiveFormsModule, Validators} from "@angular/forms";
import {MatFormField, MatLabel} from "@angular/material/form-field";
import {MatInput} from "@angular/material/input";
import {MatOption} from "@angular/material/core";
import {
  GetMovieDetailResponse,
  UpdateMovieRequest,
  UpdateMovieResponse
} from "../../../core/movie-response";
import {GenreService} from "../../../core/genre.service";
import {GetGenreResponse} from "../../../core/genre-response";
import {MatSelect} from "@angular/material/select";
import {MovieService} from "../../../core/movie.service";

@Component({
  selector: 'app-edit-movie-dialog',
  standalone: true,
  imports: [
    MatDialogTitle,
    MatDialogActions,
    MatButton,
    MatDialogContent,
    ReactiveFormsModule,
    MatFormField,
    MatInput,
    MatLabel,
    MatSelect,
    MatOption
  ],
  templateUrl: './edit-movie-dialog.component.html',
  styleUrl: './edit-movie-dialog.component.css',
})
export class EditMovieDialogComponent {
  formMovie!: FormGroup;
  formSubmitted = false;
  genres!: GetGenreResponse[];

  constructor(
    public dialogRef: MatDialogRef<EditMovieDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { movieId: number; movie: GetMovieDetailResponse },
    private fb: FormBuilder,
    private genreService: GenreService,
    private movieService: MovieService,
  ) {
  }

  ngOnInit() {
    this.genreService.getGenres().subscribe(res => {
      this.genres = res;
    });
    this.formMovie = this.fb.group({
      title: [this.data.movie.title, Validators.required],
      description: [this.data.movie.description, Validators.required],
      posterImage: [this.data.movie.posterImage, Validators.required],
      duration: [this.data.movie.duration, Validators.required],
      releaseDate: [new Date(this.data.movie.releaseDate).toISOString().split('T')[0], Validators.required],
      year: [this.data.movie.year, Validators.required],
      genreId: [this.data.movie.genre.id, Validators.required],
    });
  }

  onSubmit() {
    this.formSubmitted = true;
    const updatedMovie: UpdateMovieRequest = {
      title: this.formMovie.value!.title,
      description: this.formMovie.value!.description,
      posterImage: this.formMovie.value!.posterImage,
      duration: this.formMovie.value!.duration,
      releaseDate: this.formMovie.value!.releaseDate,
      year: this.formMovie.value!.year,
      genreId: this.formMovie.value!.genreId
    }
    this.movieService.updateMovie(this.data.movieId, updatedMovie)
      .subscribe((res: UpdateMovieResponse) => {
          this.formSubmitted = false;
          this.dialogRef.close(res);
      });
  }

  onCancel() {
    this.dialogRef.close();
  }
}
