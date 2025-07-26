import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {BehaviorSubject, Observable, tap} from "rxjs";
import {LoginRequest} from "./interfaces/login-request";
import {ValidateTokenResponse} from "./interfaces/validate-token-response";
import {LoginResponse} from "./interfaces/login-response";
import {RegisterResponse} from "./interfaces/register-response";
import {jwtDecode} from "jwt-decode";

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8080/api/v1/auth';
  private isLoggedInSubject = new BehaviorSubject<boolean>(false);
  private userSubject = new BehaviorSubject<any>(null);
  private userRolesSubject = new BehaviorSubject<string[]>([]);
  private userPermissionsSubject = new BehaviorSubject<string[]>([]);

  isLoggedIn$ = this.isLoggedInSubject.asObservable();
  user$ = this.userSubject.asObservable();
  userRoles$ = this.userRolesSubject.asObservable();
  userPermissions$ = this.userPermissionsSubject.asObservable();

  constructor(private http: HttpClient) {
  }

  register(user: { username: string; password: string }): Observable<RegisterResponse> {
    return this.http.post<RegisterResponse>(`${this.apiUrl}/register`, user);
  }

  login(user: LoginRequest): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}/login`, user)
      .pipe(
        tap(res => {
          this.userSubject.next(jwtDecode<any>(res.access_token).sub);
          this.isLoggedInSubject.next(true);
        })
      );
  }

  validateToken(): Observable<ValidateTokenResponse> {
    return this.http.post<ValidateTokenResponse>(`${this.apiUrl}/validateToken`, null)
      .pipe(tap(res => {
        if (res.isValid) {
          const token = this.getToken();
          if (token) {
            const decoded = jwtDecode<any>(token);
            this.userSubject.next(decoded.sub);
            this.isLoggedInSubject.next(true);
            this.userRolesSubject.next(decoded.roles);
            this.userPermissionsSubject.next(decoded.permissions);
          } else {
            this.userSubject.next(null);
            this.isLoggedInSubject.next(false);
            this.userRolesSubject.next([]);
            this.userPermissionsSubject.next([]);
          }
        }
      }));
  }

  getToken(): string | null {
    return localStorage.getItem('access_token');
  }

  setToken(token: string): void {
    localStorage.setItem('access_token', token);
  }

  logout(): void {
    localStorage.removeItem('access_token');
  }

  getUserPayload(): any {
    const token = this.getToken();
    if (token) {
      return jwtDecode<any>(token);
    }
  }

}
