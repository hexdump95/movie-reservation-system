import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {PageMovieResponse} from "./page-movie-response";

@Injectable({
  providedIn: 'root'
})
export class MovieService {
  private apiUrl = 'http://localhost:8080/api/v1/movies';

  constructor(private http: HttpClient) {
  }

  getUpcomingMovies(page: number): Observable<PageMovieResponse> {
    return this.http.get<PageMovieResponse>(`${this.apiUrl}?page=${page}`);
  }

  getMovieDetail(movieId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${movieId}`);
  }
}
