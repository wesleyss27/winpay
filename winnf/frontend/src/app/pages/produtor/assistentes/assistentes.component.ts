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
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatCardModule } from '@angular/material/card';
import { MatTooltipModule } from '@angular/material/tooltip';
import { ApiService } from '../../../core/services/api.service';

@Component({
  selector: 'app-assistente-dialog',
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
    <h2 mat-dialog-title>{{ data.assistente ? 'Editar Assistente' : 'Novo Assistente' }}</h2>
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
        <mat-form-field appearance="outline" class="full-width" *ngIf="!data.assistente">
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
export class AssistenteDialogComponent {
  form: FormGroup;
  isLoading = false;

  constructor(
    private fb: FormBuilder,
    public dialogRef: MatDialogRef<AssistenteDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { assistente: any }
  ) {
    this.form = this.fb.group({
      name: [data.assistente?.name ?? '', Validators.required],
      email: [data.assistente?.email ?? '', [Validators.required, Validators.email]],
      password: [
        '',
        data.assistente ? [] : [Validators.required, Validators.minLength(6)],
      ],
    });
  }

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    const value = { ...this.form.value };
    if (this.data.assistente && !value.password) {
      delete value.password;
    }
    this.dialogRef.close(value);
  }
}

@Component({
  selector: 'app-assistentes',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatCardModule,
    MatTooltipModule,
    MatSnackBarModule,
    MatDialogModule,
  ],
  templateUrl: './assistentes.component.html',
  styleUrls: ['./assistentes.component.scss'],
})
export class AssistentesComponent implements OnInit {
  displayedColumns = ['name', 'email', 'active', 'actions'];
  assistentes: any[] = [];
  isLoading = false;

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
    this.api.get<any>('/produtor/assistentes').subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.assistentes = res.data.items ?? res.data;
        }
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Erro ao carregar assistentes', 'Fechar', { duration: 3000 });
      },
    });
  }

  openDialog(assistente?: any): void {
    const ref = this.dialog.open(AssistenteDialogComponent, {
      data: { assistente: assistente ?? null },
      width: '480px',
    });

    ref.afterClosed().subscribe((result) => {
      if (!result) return;
      if (assistente) {
        this.api.put<any>(`/produtor/assistentes/${assistente.id}`, result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Assistente atualizado!', 'Fechar', { duration: 3000 });
              this.load();
            } else {
              this.snackBar.open(res.message || 'Erro', 'Fechar', { duration: 3000 });
            }
          },
          error: (err) => {
            this.snackBar.open(err?.error?.message || 'Erro ao atualizar', 'Fechar', { duration: 3000 });
          },
        });
      } else {
        this.api.post<any>('/produtor/assistentes', result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Assistente criado!', 'Fechar', { duration: 3000 });
              this.load();
            } else {
              this.snackBar.open(res.message || 'Erro', 'Fechar', { duration: 3000 });
            }
          },
          error: (err) => {
            this.snackBar.open(err?.error?.message || 'Erro ao criar', 'Fechar', { duration: 3000 });
          },
        });
      }
    });
  }

  deleteAssistente(assistente: any): void {
    if (!confirm(`Desativar assistente ${assistente.name}?`)) return;
    this.api.delete<any>(`/produtor/assistentes/${assistente.id}`).subscribe({
      next: (res) => {
        if (res.success) {
          this.snackBar.open('Assistente removido!', 'Fechar', { duration: 3000 });
          this.load();
        } else {
          this.snackBar.open(res.message || 'Erro', 'Fechar', { duration: 3000 });
        }
      },
      error: () => {
        this.snackBar.open('Erro ao remover assistente', 'Fechar', { duration: 3000 });
      },
    });
  }
}
