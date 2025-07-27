import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";
import {GetGenreResponse} from "./genre-response";

@Injectable({
  providedIn: 'root'
})
export class GenreService {
  private apiUrl = "http://localhost:8080/api/v1/genres";

  constructor(private http: HttpClient) {
  }

  public getGenres(): Observable<GetGenreResponse[]> {
    return this.http.get<GetGenreResponse[]>(`${this.apiUrl}`);
  }

}
