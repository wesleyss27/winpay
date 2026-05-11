import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, MatProgressSpinnerModule],
  template: `
    <div class="dashboard-redirect">
      <mat-spinner diameter="48"></mat-spinner>
      <p>Redirecionando...</p>
    </div>
  `,
  styles: [`
    .dashboard-redirect {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 16px;
      color: #1a237e;
      font-size: 16px;
    }
  `],
})
export class DashboardComponent implements OnInit {
  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit(): void {
    const role = this.authService.getRole();
    if (role === 'admin') {
      this.router.navigate(['/admin/produtores']);
    } else if (role === 'produtor') {
      this.router.navigate(['/produtor/assistentes']);
    } else {
      this.router.navigate(['/notas']);
    }
  }
}
