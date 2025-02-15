import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {ShowtimeResponse} from "./showtime/showtime-response";

@Injectable({
  providedIn: 'root'
})
export class BookService {
  private apiUrl = 'http://localhost:8080/api/v1/book';

  constructor(private http: HttpClient) {
  }

  getSeatsByShowtimeId(showtimeId: number): Observable<ShowtimeResponse> {
    return this.http.get<ShowtimeResponse>(`${this.apiUrl}/showtimes/${showtimeId}`);
  }
}
