import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { ReactiveFormsModule, FormControl, Validators } from '@angular/forms';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, MatIconModule, MatProgressSpinnerModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent {
  emailControl    = new FormControl('', [Validators.required, Validators.email]);
  passwordControl = new FormControl('', [Validators.required, Validators.minLength(6)]);

  isLoading    = false;
  errorMessage = '';
  hidePassword = true;
  currentYear  = new Date().getFullYear();

  constructor(
    private authService: AuthService,
    private router: Router,
  ) {}

  onSubmit(): void {
    if (this.emailControl.invalid || this.passwordControl.invalid) {
      this.errorMessage = 'Preencha e-mail e senha corretamente.';
      return;
    }

    this.isLoading    = true;
    this.errorMessage = '';

    this.authService.login(this.emailControl.value!, this.passwordControl.value!).subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.router.navigate(['/dashboard']);
        } else {
          this.errorMessage = res.message || 'Credenciais inválidas.';
        }
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = `Erro ${err?.status}: ${err?.error?.message || err?.message || 'Sem resposta'}`;
      },
    });
  }
}
