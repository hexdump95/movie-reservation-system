import {Component} from '@angular/core';
import {MatButton} from "@angular/material/button";
import {
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from "@angular/material/dialog";
import {MatFormField, MatLabel} from "@angular/material/form-field";
import {MatInput} from "@angular/material/input";
import {MatOption} from "@angular/material/core";
import {MatSelect} from "@angular/material/select";
import {FormBuilder, FormGroup, ReactiveFormsModule, Validators} from "@angular/forms";
import {GetGenreResponse} from "../../../core/genre-response";
import {CreateMovieRequest, CreateMovieResponse} from "../../../core/movie-response";
import {GenreService} from "../../../core/genre.service";
import {MovieService} from "../../../core/movie.service";

@Component({
  selector: 'app-create-movie-dialog',
  standalone: true,
  imports: [
    MatButton,
    MatDialogActions,
    MatDialogContent,
    MatDialogTitle,
    MatFormField,
    MatInput,
    MatLabel,
    MatOption,
    MatSelect,
    ReactiveFormsModule
  ],
  templateUrl: './create-movie-dialog.component.html',
  styleUrl: './create-movie-dialog.component.css'
})
export class CreateMovieDialogComponent {

  formMovie!: FormGroup;
  formSubmitted = false;
  genres!: GetGenreResponse[];

  constructor(
    public dialogRef: MatDialogRef<CreateMovieDialogComponent>,
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
      title: ['', Validators.required],
      description: ['', Validators.required],
      posterImage: ['', Validators.required],
      duration: [0, Validators.required],
      releaseDate: [null, Validators.required],
      year: [0, Validators.required],
      genreId: [null, Validators.required],
    });
  }

  onSubmit() {
    this.formSubmitted = true;
    const createdMovie: CreateMovieRequest = {
      title: this.formMovie.value!.title,
      description: this.formMovie.value!.description,
      posterImage: this.formMovie.value!.posterImage,
      duration: this.formMovie.value!.duration,
      releaseDate: this.formMovie.value!.releaseDate,
      year: this.formMovie.value!.year,
      genreId: this.formMovie.value!.genreId
    }
    this.movieService.createMovie(createdMovie)
      .subscribe((res: CreateMovieResponse) => {
        this.formSubmitted = false;
        this.dialogRef.close(res);
      });
  }

  onCancel() {
    this.dialogRef.close();
  }
}
