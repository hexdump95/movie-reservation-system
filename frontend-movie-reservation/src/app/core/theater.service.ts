import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {TheaterResponse} from "./theater-response";
import {UnavailableDate} from "./movie-response";

@Injectable({
  providedIn: 'root'
})
export class TheaterService {
  private apiUrl = 'http://localhost:8080/api/v1/theaters';

  constructor(private http: HttpClient) {
  }

  public getTheaters(): Observable<TheaterResponse[]> {
    return this.http.get<TheaterResponse[]>(`${this.apiUrl}`);
  }

  getUnavailableDates(id: number): Observable<UnavailableDate[]> {
    return this.http.get<UnavailableDate[]>(`${this.apiUrl}/${id}/unavailable-dates`);
  }
}
