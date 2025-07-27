import {Component} from '@angular/core';
import {MovieService} from "../core/movie.service";
import {PageMovieResponse} from "./page-movie-response";
import {NgStyle} from "@angular/common";
import {MovieDetailComponent} from "./movie-detail/movie-detail.component";

@Component({
  selector: 'app-movie',
  standalone: true,
  imports: [
    NgStyle,
    MovieDetailComponent,
  ],
  templateUrl: './movie.component.html',
  styleUrl: './movie.component.css'
})
export class MovieComponent {
  currentPage = 1;
  page!: PageMovieResponse;
  loadingPage: boolean = false;

  constructor(private movieService: MovieService) {
  }

  minPageNumber = 1;
  maxPageNumber = 10;
  pageRange: number[] = [];
  isModalOpen: boolean = false;
  selectedMovieId!: number;

  updatePageRange(): void {
    if (this.currentPage >= 6) {
      if (this.currentPage + 4 > this.page.totalPages) {
        this.minPageNumber = this.page.totalPages - 9;
        this.maxPageNumber = this.page.totalPages;
      } else {
        this.minPageNumber = this.currentPage - 5;
        this.maxPageNumber = this.currentPage + 4;
      }
    } else {
      this.minPageNumber = 1;
      this.maxPageNumber = this.page.totalPages <= 10 ? this.page.totalPages : 10;
    }
    let range = [];
    for (let i = this.minPageNumber; i <= this.maxPageNumber; i++) {
      range.push(i);
    }
    this.pageRange = range;
  }

  ngOnInit() {
    this.movieService.getUpcomingMovies(this.currentPage)
      .subscribe((response: PageMovieResponse) => {
        this.page = response;
        this.maxPageNumber = response.totalPages >= 10 ? 10 : response.totalPages;
        this.updatePageRange();
      });
  }

  previousPage() {
    this.movieService.getUpcomingMovies(--this.currentPage)
      .subscribe((response: PageMovieResponse) => {
        this.page = response;
        this.loadingPage = false;
        this.updatePageRange();
      });
  }

  nextPage() {
    this.movieService.getUpcomingMovies(++this.currentPage)
      .subscribe((response: PageMovieResponse) => {
        this.page = response;
        this.loadingPage = false;
        this.updatePageRange();
      });
  }

  goToPage(page: number) {
    this.movieService.getUpcomingMovies(page)
      .subscribe((response: PageMovieResponse) => {
        this.page = response;
        this.currentPage = page;
        this.loadingPage = false;
        this.updatePageRange();
      });
  }

  openMovieDetail(movieId: number) {
    this.selectedMovieId = movieId;
    this.isModalOpen = true;
  }

  closeModal() {
    this.isModalOpen = false;
  }
}
