import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { Router } from '@angular/router';
import { User, UserRole } from '../models/user.model';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly TOKEN_KEY = 'winnf_token';
  private readonly REFRESH_KEY = 'winnf_refresh_token';
  private readonly USER_KEY = 'winnf_user';

  currentUser$ = new BehaviorSubject<User | null>(this.getStoredUser());

  private readonly apiUrl = '/api';

  constructor(private http: HttpClient, private router: Router) {}

  login(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/auth/login`, { email, password }).pipe(
      tap(res => {
        if (res.success) {
          localStorage.setItem(this.TOKEN_KEY, res.data.access_token);
          localStorage.setItem(this.REFRESH_KEY, res.data.refresh_token);
          localStorage.setItem(this.USER_KEY, JSON.stringify(res.data.user));
          this.currentUser$.next(res.data.user);
        }
      })
    );
  }

  logout(): void {
    this.http.post(`${this.apiUrl}/auth/logout`, {}).subscribe();
    localStorage.removeItem(this.TOKEN_KEY);
    localStorage.removeItem(this.REFRESH_KEY);
    localStorage.removeItem(this.USER_KEY);
    this.currentUser$.next(null);
    this.router.navigate(['/login']);
  }

  refreshToken(): Observable<any> {
    const refresh = localStorage.getItem(this.REFRESH_KEY);
    return this.http.post<any>(`${this.apiUrl}/auth/refresh`, {}, {
      headers: { Authorization: `Bearer ${refresh}` }
    }).pipe(
      tap(res => {
        if (res.success) {
          localStorage.setItem(this.TOKEN_KEY, res.data.access_token);
        }
      })
    );
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY);
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }

  getRole(): UserRole | null {
    return this.getStoredUser()?.role ?? null;
  }

  private getStoredUser(): User | null {
    const stored = localStorage.getItem(this.USER_KEY);
    return stored ? JSON.parse(stored) : null;
  }
}
