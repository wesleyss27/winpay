import { Routes } from '@angular/router';
import { guardaAuth } from './guards/auth.guard';

export const rotas: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  {
    path: 'login',
    loadComponent: () => import('./paginas/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: 'admin',
    canActivate: [guardaAuth('admin')],
    loadComponent: () => import('./paginas/admin/admin.component').then(m => m.AdminComponent),
    children: [
      { path: '', redirectTo: 'clientes', pathMatch: 'full' },
      { path: 'clientes', loadComponent: () => import('./paginas/admin/clientes/clientes.component').then(m => m.ClientesComponent) },
      { path: 'tickets', loadComponent: () => import('./paginas/admin/tickets/tickets.component').then(m => m.TicketsComponent) },
    ]
  },
  {
    path: 'painel',
    canActivate: [guardaAuth('cliente')],
    loadComponent: () => import('./paginas/cliente/cliente.component').then(m => m.ClienteComponent),
    children: [
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      { path: 'dashboard', loadComponent: () => import('./paginas/cliente/dashboard/dashboard.component').then(m => m.DashboardComponent) },
      { path: 'suporte', loadComponent: () => import('./paginas/cliente/suporte/suporte.component').then(m => m.SuporteComponent) },
      { path: 'cancelar', loadComponent: () => import('./paginas/cliente/cancelar/cancelar.component').then(m => m.CancelarComponent) },
    ]
  },
  { path: '**', redirectTo: 'login' }
];
