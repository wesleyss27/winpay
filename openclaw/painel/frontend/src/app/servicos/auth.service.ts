import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from './api.service';
import { tap } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class AuthService {
  constructor(private api: ApiService, private router: Router) {}

  login(email: string, senha: string) {
    return this.api.login(email, senha).pipe(
      tap(res => {
        localStorage.setItem('token', res.access_token);
        localStorage.setItem('papel', res.papel);
        localStorage.setItem('nome', res.nome);
      })
    );
  }

  logout() {
    localStorage.clear();
    this.router.navigate(['/login']);
  }

  obterToken(): string | null {
    return localStorage.getItem('token');
  }

  obterPapel(): string | null {
    return localStorage.getItem('papel');
  }

  estaLogado(): boolean {
    return !!this.obterToken();
  }
}
