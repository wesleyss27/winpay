import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../servicos/auth.service';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, RouterLinkActive],
  template: `
    <div class="layout">
      <nav class="sidebar">
        <div class="logo">Painel Admin</div>
        <a routerLink="/admin/clientes" routerLinkActive="ativo">👥 Clientes</a>
        <a routerLink="/admin/tickets" routerLinkActive="ativo">🎧 Suporte</a>
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
    .logo { color: #fff; font-weight: 700; font-size: 1.1rem; margin-bottom: 1.5rem; padding: 0 0.5rem; }
    a {
      color: #aaa; text-decoration: none; padding: 0.75rem 0.75rem;
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
export class AdminComponent {
  constructor(public auth: AuthService) {}
}
