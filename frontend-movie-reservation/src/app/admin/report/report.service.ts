import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class ReportService {
  private apiUrl = 'http://localhost:8080/api/v1/reports';

  constructor(private http: HttpClient) {
  }

  public getRevenue(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/revenue`);
  }
}
