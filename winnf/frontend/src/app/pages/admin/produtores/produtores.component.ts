import { Component, OnInit, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatDialogModule, MatDialog, MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatCardModule } from '@angular/material/card';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { ApiService } from '../../../core/services/api.service';

// --- Dialog Component ---
@Component({
  selector: 'app-produtor-dialog',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatDialogModule,
    MatProgressSpinnerModule,
  ],
  template: `
    <h2 mat-dialog-title>{{ data.produtor ? 'Editar Produtor' : 'Novo Produtor' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" novalidate class="dialog-form">
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Nome</mat-label>
          <input matInput formControlName="name" />
          <mat-error *ngIf="form.get('name')?.hasError('required')">Nome é obrigatório</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>E-mail</mat-label>
          <input matInput type="email" formControlName="email" />
          <mat-error *ngIf="form.get('email')?.hasError('required')">E-mail é obrigatório</mat-error>
          <mat-error *ngIf="form.get('email')?.hasError('email')">E-mail inválido</mat-error>
        </mat-form-field>
        <mat-form-field appearance="outline" class="full-width" *ngIf="!data.produtor">
          <mat-label>Senha</mat-label>
          <input matInput type="password" formControlName="password" />
          <mat-error *ngIf="form.get('password')?.hasError('required')">Senha é obrigatória</mat-error>
          <mat-error *ngIf="form.get('password')?.hasError('minlength')">Mínimo 6 caracteres</mat-error>
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancelar</button>
      <button mat-raised-button color="primary" (click)="save()" [disabled]="isLoading">
        <mat-spinner diameter="18" *ngIf="isLoading"></mat-spinner>
        <span *ngIf="!isLoading">Salvar</span>
      </button>
    </mat-dialog-actions>
  `,
  styles: [`
    .dialog-form { display: flex; flex-direction: column; gap: 4px; min-width: 360px; padding-top: 8px; }
    .full-width { width: 100%; }
    mat-dialog-content { overflow: visible; }
  `],
})
export class ProdutorDialogComponent {
  form: FormGroup;
  isLoading = false;

  constructor(
    private fb: FormBuilder,
    public dialogRef: MatDialogRef<ProdutorDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { produtor: any }
  ) {
    this.form = this.fb.group({
      name: [data.produtor?.name ?? '', Validators.required],
      email: [data.produtor?.email ?? '', [Validators.required, Validators.email]],
      password: [
        '',
        data.produtor ? [] : [Validators.required, Validators.minLength(6)],
      ],
    });
  }

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    const value = { ...this.form.value };
    if (this.data.produtor && !value.password) {
      delete value.password;
    }
    this.dialogRef.close(value);
  }
}

// --- Main Component ---
@Component({
  selector: 'app-produtores',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatProgressSpinnerModule,
    MatCardModule,
    MatToolbarModule,
    MatTooltipModule,
    MatPaginatorModule,
    MatSnackBarModule,
    MatDialogModule,
  ],
  templateUrl: './produtores.component.html',
  styleUrls: ['./produtores.component.scss'],
})
export class ProdutoresComponent implements OnInit {
  displayedColumns = ['name', 'email', 'active', 'created_at', 'actions'];
  produtores: any[] = [];
  isLoading = false;
  totalItems = 0;
  pageSize = 20;
  currentPage = 1;

  constructor(
    private api: ApiService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.isLoading = true;
    this.api.get<any>('/admin/produtores', { page: this.currentPage, per_page: this.pageSize }).subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.produtores = res.data.items ?? res.data;
          this.totalItems = res.data.total ?? this.produtores.length;
        }
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Erro ao carregar produtores', 'Fechar', { duration: 3000 });
      },
    });
  }

  onPageChange(event: PageEvent): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.load();
  }

  openDialog(produtor?: any): void {
    const ref = this.dialog.open(ProdutorDialogComponent, {
      data: { produtor: produtor ?? null },
      width: '480px',
    });

    ref.afterClosed().subscribe((result) => {
      if (!result) return;
      if (produtor) {
        this.api.put<any>(`/admin/produtores/${produtor.id}`, result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Produtor atualizado!', 'Fechar', { duration: 3000 });
              this.load();
            } else {
              this.snackBar.open(res.message || 'Erro ao atualizar', 'Fechar', { duration: 3000 });
            }
          },
          error: (err) => {
            this.snackBar.open(err?.error?.message || 'Erro ao atualizar', 'Fechar', { duration: 3000 });
          },
        });
      } else {
        this.api.post<any>('/admin/produtores', result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Produtor criado!', 'Fechar', { duration: 3000 });
              this.load();
            } else {
              this.snackBar.open(res.message || 'Erro ao criar', 'Fechar', { duration: 3000 });
            }
          },
          error: (err) => {
            this.snackBar.open(err?.error?.message || 'Erro ao criar', 'Fechar', { duration: 3000 });
          },
        });
      }
    });
  }

  toggleAtivo(produtor: any): void {
    const action = produtor.active ? 'desativar' : 'ativar';
    this.api.put<any>(`/admin/produtores/${produtor.id}`, { active: !produtor.active }).subscribe({
      next: (res) => {
        if (res.success) {
          this.snackBar.open(`Produtor ${action}do com sucesso!`, 'Fechar', { duration: 3000 });
          this.load();
        } else {
          this.snackBar.open(res.message || 'Erro', 'Fechar', { duration: 3000 });
        }
      },
      error: () => {
        this.snackBar.open('Erro ao alterar status', 'Fechar', { duration: 3000 });
      },
    });
  }
}
