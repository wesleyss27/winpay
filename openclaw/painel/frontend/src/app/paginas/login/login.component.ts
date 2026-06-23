import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../servicos/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="login-container">
      <div class="login-card">
        <h1>Painel OpenClaw</h1>
        <p class="subtitulo">Acesse sua conta</p>

        <form (ngSubmit)="entrar()">
          <div class="campo">
            <label>E-mail</label>
            <input type="email" [(ngModel)]="email" name="email" placeholder="seu@email.com" required />
          </div>
          <div class="campo">
            <label>Senha</label>
            <input type="password" [(ngModel)]="senha" name="senha" placeholder="••••••••" required />
          </div>
          <div class="erro" *ngIf="erro">{{ erro }}</div>
          <button type="submit" [disabled]="carregando">
            {{ carregando ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>
      </div>
    </div>
  `,
  styles: [`
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #0f0f0f;
    }
    .login-card {
      background: #1a1a1a;
      border: 1px solid #2a2a2a;
      border-radius: 12px;
      padding: 2.5rem;
      width: 100%;
      max-width: 400px;
    }
    h1 { color: #fff; font-size: 1.5rem; margin: 0 0 0.25rem; }
    .subtitulo { color: #666; margin: 0 0 2rem; }
    .campo { margin-bottom: 1rem; }
    label { display: block; color: #aaa; font-size: 0.875rem; margin-bottom: 0.4rem; }
    input {
      width: 100%; padding: 0.75rem; background: #111;
      border: 1px solid #333; border-radius: 8px;
      color: #fff; font-size: 1rem; box-sizing: border-box;
    }
    button {
      width: 100%; padding: 0.875rem; background: #e53e3e;
      color: #fff; border: none; border-radius: 8px;
      font-size: 1rem; font-weight: 600; cursor: pointer; margin-top: 1rem;
    }
    button:disabled { opacity: 0.6; cursor: not-allowed; }
    .erro { color: #fc8181; font-size: 0.875rem; margin-bottom: 0.5rem; }
  `]
})
export class LoginComponent {
  email = '';
  senha = '';
  erro = '';
  carregando = false;

  constructor(private auth: AuthService, private router: Router) {}

  entrar() {
    this.erro = '';
    this.carregando = true;
    this.auth.login(this.email, this.senha).subscribe({
      next: (res) => {
        this.router.navigate([res.papel === 'admin' ? '/admin' : '/painel']);
      },
      error: () => {
        this.erro = 'E-mail ou senha incorretos';
        this.carregando = false;
      }
    });
  }
}
