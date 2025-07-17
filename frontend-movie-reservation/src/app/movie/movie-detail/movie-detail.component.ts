import {Component, EventEmitter, Input, Output} from '@angular/core';
import {MovieService} from "../movie.service";
import {DatePipe} from "@angular/common";
import {RouterLink} from "@angular/router";

@Component({
  selector: 'app-movie-detail',
  standalone: true,
  imports: [
    DatePipe,
    RouterLink
  ],
  templateUrl: './movie-detail.component.html',
  styleUrl: './movie-detail.component.css'
})
export class MovieDetailComponent {
  @Input() movieId!: number;
  @Output() closeModal = new EventEmitter<void>();

  movie!: any;
  currentDate = new Date();

  constructor(private movieService: MovieService) {
  }

  ngOnInit() {
    this.movieService.getMovieDetail(this.movieId)
      .subscribe(movie => {
        this.movie = movie;
      });
  }

  close() {
    this.closeModal.emit();
  }
}
