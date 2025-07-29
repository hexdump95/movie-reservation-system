import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {PageMovieResponse} from "../movie/page-movie-response";
import {
  CreateMovieRequest,
  CreateMovieResponse,
  GetMovieDetailResponse,
  GetMovieResponse, GetShowtimeResponse,
  UpdateMovieResponse
} from "./movie-response";

@Injectable({
  providedIn: 'root'
})
export class MovieService {
  private apiUrl = 'http://localhost:8080/api/v1/movies';

  constructor(private http: HttpClient) {
  }

  getUpcomingMovies(page: number): Observable<PageMovieResponse> {
    return this.http.get<PageMovieResponse>(`${this.apiUrl}/upcoming?page=${page}`);
  }

  getMovieDetail(movieId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/upcoming/${movieId}`);
  }

  getMovies(): Observable<GetMovieResponse[]> {
    return this.http.get<GetMovieResponse[]>(`${this.apiUrl}`);
  }

  getMovie(id: number): Observable<GetMovieDetailResponse> {
    return this.http.get<GetMovieDetailResponse>(`${this.apiUrl}/${id}`);
  }

  createMovie(movie: CreateMovieRequest): Observable<CreateMovieResponse> {
    return this.http.post<CreateMovieResponse>(`${this.apiUrl}`, movie);
  }

  updateMovie(id: number, movie: any): Observable<UpdateMovieResponse> {
    return this.http.put<UpdateMovieResponse>(`${this.apiUrl}/${id}`, movie);
  }

  deleteMovie(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

  getShowtimesByMovieId(movieId: number): Observable<GetShowtimeResponse[]> {
    return this.http.get<GetShowtimeResponse[]>(`${this.apiUrl}/${movieId}/showtimes`);
  }

}
