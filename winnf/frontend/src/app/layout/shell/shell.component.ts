import { Component, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { BreakpointObserver } from '@angular/cdk/layout';
import { MatSidenav, MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatListModule } from '@angular/material/list';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { MatMenuModule } from '@angular/material/menu';
import { AuthService } from '../../core/services/auth.service';
import { User, UserRole } from '../../core/models/user.model';

@Component({
  selector: 'app-shell',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    MatSidenavModule,
    MatToolbarModule,
    MatListModule,
    MatIconModule,
    MatButtonModule,
    MatMenuModule,
  ],
  templateUrl: './shell.component.html',
  styleUrls: ['./shell.component.scss'],
})
export class ShellComponent implements OnInit {
  @ViewChild('sidenav') sidenav!: MatSidenav;

  currentUser: User | null = null;
  role: UserRole | null = null;
  isMobile = false;

  adminLinks = [
    { label: 'Produtores',   icon: 'business',      path: '/admin/produtores' },
    { label: 'Notas Fiscais', icon: 'receipt_long',  path: '/admin/notas' },
  ];

  produtorLinks = [
    { label: 'Assistentes',       icon: 'people',        path: '/produtor/assistentes' },
    { label: 'Integração Notazz', icon: 'settings',      path: '/produtor/integracao' },
    { label: 'Clientes',          icon: 'contacts',      path: '/produtor/clientes' },
    { label: 'Produtos/Serviços', icon: 'inventory_2',   path: '/produtor/produtos' },
    { label: 'Notas Fiscais',     icon: 'receipt_long',  path: '/notas' },
  ];

  assistenteLinks = [
    { label: 'Emitir Nota',     icon: 'add_circle',    path: '/notas/emitir' },
    { label: 'Minhas Notas',    icon: 'receipt_long',  path: '/notas' },
    { label: 'Emissão em Lote', icon: 'dynamic_feed',  path: '/notas/lote' },
  ];

  constructor(
    private authService: AuthService,
    private router: Router,
    private breakpointObserver: BreakpointObserver,
  ) {}

  ngOnInit(): void {
    this.authService.currentUser$.subscribe(user => {
      this.currentUser = user;
      this.role = user?.role ?? null;
    });

    this.breakpointObserver.observe('(max-width: 768px)').subscribe(result => {
      this.isMobile = result.matches;
    });
  }

  get navLinks() {
    if (this.role === 'admin')      return this.adminLinks;
    if (this.role === 'produtor')   return this.produtorLinks;
    if (this.role === 'assistente') return this.assistenteLinks;
    return [];
  }

  get roleLabel(): string {
    const labels: Record<string, string> = {
      admin: 'Admin', produtor: 'Produtor', assistente: 'Assistente',
    };
    return this.role ? labels[this.role] : '';
  }

  onNavClick(): void {
    if (this.isMobile) this.sidenav.close();
  }

  logout(): void {
    this.authService.logout();
  }
}
