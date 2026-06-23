import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../servicos/auth.service';

@Component({
  selector: 'app-cliente',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, RouterLinkActive],
  template: `
    <div class="layout">
      <nav class="sidebar">
        <div class="logo">Meu Painel</div>
        <div class="usuario">{{ nome }}</div>
        <a routerLink="/painel/dashboard" routerLinkActive="ativo">📊 Dashboard</a>
        <a routerLink="/painel/suporte" routerLinkActive="ativo">🎧 Suporte</a>
        <a routerLink="/painel/cancelar" routerLinkActive="ativo">❌ Cancelamento</a>
        <button (click)="auth.logout()">Sair</button>
      </nav>
      <main class="conteudo">
        <router-outlet />
      </main>
    </div>
  `,
  styles: [`
    .layout { display: flex; min-height: 100vh; background: #0f0f0f; }
    .sidebar {
      width: 220px; background: #1a1a1a; border-right: 1px solid #2a2a2a;
      display: flex; flex-direction: column; padding: 1.5rem 1rem; gap: 0.5rem;
    }
    .logo { color: #fff; font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem; padding: 0 0.5rem; }
    .usuario { color: #666; font-size: 0.8rem; margin-bottom: 1rem; padding: 0 0.5rem; }
    a {
      color: #aaa; text-decoration: none; padding: 0.75rem;
      border-radius: 8px; font-size: 0.9rem;
    }
    a.ativo, a:hover { background: #2a2a2a; color: #fff; }
    button {
      margin-top: auto; padding: 0.75rem; background: #2a2a2a;
      color: #aaa; border: none; border-radius: 8px; cursor: pointer;
    }
    .conteudo { flex: 1; padding: 2rem; color: #fff; }
  `]
})
export class ClienteComponent {
  nome = localStorage.getItem('nome') || '';
  constructor(public auth: AuthService) {}
}
