import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {ShowtimeResponse} from "./showtime/showtime-response";

@Injectable({
  providedIn: 'root'
})
export class BookService {
  private apiUrl = 'http://localhost:8080/api/v1/books';

  constructor(private http: HttpClient) {
  }

  getSeatsByShowtimeId(showtimeId: number): Observable<ShowtimeResponse> {
    return this.http.get<ShowtimeResponse>(`${this.apiUrl}/showtimes/${showtimeId}`);
  }

  temporaryBookSeat(showtimeId: number, seatId: number): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/showtimes/${showtimeId}/seats/${seatId}`, {});
  }

  getCentrifugoToken(showtimeId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/getCentrifugoToken/${showtimeId}`);
  }

  holdSeats(showtimeId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/showtimes/${showtimeId}/hold`, {});
  }

  paySeats(showtimeId: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/showtimes/${showtimeId}/pay`, {});
  }

  getReservations(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}`);
  }

  getOneReservation(bookId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${bookId}`);
  }

  cancelReservation(bookId: number): Observable<any> {
    return this.http.patch<any>(`${this.apiUrl}/${bookId}/cancel`, {});
  }

}
