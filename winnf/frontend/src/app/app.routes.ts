import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';
import { roleGuard } from './core/guards/role.guard';

export const routes: Routes = [
  { path: '', redirectTo: '/dashboard', pathMatch: 'full' },
  {
    path: 'login',
    loadComponent: () => import('./pages/login/login.component').then(m => m.LoginComponent)
  },
  {
    path: 'dashboard',
    canActivate: [authGuard],
    loadComponent: () => import('./pages/dashboard/dashboard.component').then(m => m.DashboardComponent)
  },
  {
    path: '',
    canActivate: [authGuard],
    loadComponent: () => import('./layout/shell/shell.component').then(m => m.ShellComponent),
    children: [
      {
        path: 'admin',
        canActivate: [roleGuard],
        data: { roles: ['admin'] },
        children: [
          { path: '', redirectTo: 'produtores', pathMatch: 'full' },
          {
            path: 'produtores',
            loadComponent: () => import('./pages/admin/produtores/produtores.component').then(m => m.ProdutoresComponent)
          },
          {
            path: 'notas',
            loadComponent: () => import('./pages/admin/notas/admin-notas.component').then(m => m.AdminNotasComponent)
          }
        ]
      },
      {
        path: 'produtor',
        canActivate: [roleGuard],
        data: { roles: ['produtor'] },
        children: [
          { path: '', redirectTo: 'assistentes', pathMatch: 'full' },
          {
            path: 'assistentes',
            loadComponent: () => import('./pages/produtor/assistentes/assistentes.component').then(m => m.AssistentesComponent)
          },
          {
            path: 'integracao',
            loadComponent: () => import('./pages/produtor/integracao/integracao.component').then(m => m.IntegracaoComponent)
          },
          {
            path: 'clientes',
            loadComponent: () => import('./pages/produtor/clientes/clientes.component').then(m => m.ClientesComponent)
          },
          {
            path: 'produtos',
            loadComponent: () => import('./pages/produtor/produtos/produtos.component').then(m => m.ProdutosComponent)
          }
        ]
      },
      {
        path: 'notas',
        children: [
          {
            path: '',
            loadComponent: () => import('./pages/notas/lista/notas-lista.component').then(m => m.NotasListaComponent)
          },
          {
            path: 'emitir',
            loadComponent: () => import('./pages/notas/emitir/emitir-nota.component').then(m => m.EmitirNotaComponent)
          },
          {
            path: 'lote',
            loadComponent: () => import('./pages/notas/lote/lote.component').then(m => m.LoteComponent)
          }
        ]
      }
    ]
  },
  { path: '**', redirectTo: '/dashboard' }
];
