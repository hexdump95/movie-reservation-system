import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {CreateTheaterRequest, TheaterDetailResponse, TheaterResponse} from "./theater-response";
import {UnavailableDate} from "./movie-response";

@Injectable({
  providedIn: 'root'
})
export class TheaterService {
  private apiUrl = 'http://localhost:8080/api/v1/theaters';

  constructor(private http: HttpClient) {
  }

  getTheaters(): Observable<TheaterResponse[]> {
    return this.http.get<TheaterResponse[]>(`${this.apiUrl}`);
  }

  getTheater(id: number): Observable<TheaterDetailResponse> {
    return this.http.get<TheaterDetailResponse>(`${this.apiUrl}/${id}`);
  }

  getUnavailableDates(id: number): Observable<UnavailableDate[]> {
    return this.http.get<UnavailableDate[]>(`${this.apiUrl}/${id}/unavailable-dates`);
  }

  createTheater(theater: CreateTheaterRequest): Observable<any> {
    return this.http.post<TheaterResponse>(`${this.apiUrl}`, theater);
  }

  deleteTheater(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

}
