import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {Observable} from "rxjs";

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = 'http://localhost:8080/api/v1/users';

  constructor(private http: HttpClient) {
  }

  public getUsersAndRoles(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/users-and-roles`);
  }

  public updateRoleUser(userId: number, role: string): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/users/${userId}/role`, {role});
  }
}
