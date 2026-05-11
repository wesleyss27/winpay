import { Component, OnInit, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatDialogModule, MatDialog, MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatCardModule } from '@angular/material/card';
import { MatTooltipModule } from '@angular/material/tooltip';
import { ApiService } from '../../../core/services/api.service';

@Component({
  selector: 'app-produto-dialog',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatDialogModule,
    MatProgressSpinnerModule,
  ],
  template: `
    <h2 mat-dialog-title>{{ data.produto ? 'Editar Produto/Serviço' : 'Novo Produto/Serviço' }}</h2>
    <mat-dialog-content>
      <form [formGroup]="form" novalidate class="dialog-form">
        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Nome</mat-label>
          <input matInput formControlName="nome" />
          <mat-error *ngIf="form.get('nome')?.hasError('required')">Nome é obrigatório</mat-error>
        </mat-form-field>

        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Tipo</mat-label>
          <mat-select formControlName="tipo">
            <mat-option value="produto">Produto</mat-option>
            <mat-option value="servico">Serviço</mat-option>
          </mat-select>
          <mat-error *ngIf="form.get('tipo')?.hasError('required')">Tipo é obrigatório</mat-error>
        </mat-form-field>

        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>CNAE</mat-label>
            <input matInput formControlName="cnae" placeholder="Ex: 6201-5/01" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>LC116</mat-label>
            <input matInput formControlName="lc116" placeholder="Ex: 1.01" />
          </mat-form-field>
        </div>

        <mat-form-field appearance="outline" class="full-width">
          <mat-label>Valor Padrão (R$)</mat-label>
          <input matInput type="number" min="0" step="0.01" formControlName="valor_padrao" />
        </mat-form-field>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancelar</button>
      <button mat-raised-button color="primary" (click)="save()">Salvar</button>
    </mat-dialog-actions>
  `,
  styles: [`
    .dialog-form { display: flex; flex-direction: column; gap: 4px; min-width: 420px; padding-top: 8px; }
    .full-width { width: 100%; }
    .form-row { display: flex; gap: 12px; }
    mat-form-field { flex: 1; }
    mat-dialog-content { overflow: visible; }
  `],
})
export class ProdutoDialogComponent {
  form: FormGroup;

  constructor(
    private fb: FormBuilder,
    public dialogRef: MatDialogRef<ProdutoDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { produto: any }
  ) {
    const p = data.produto;
    this.form = this.fb.group({
      nome: [p?.nome ?? '', Validators.required],
      tipo: [p?.tipo ?? 'servico', Validators.required],
      cnae: [p?.cnae ?? ''],
      lc116: [p?.lc116 ?? ''],
      valor_padrao: [p?.valor_padrao ?? ''],
    });
  }

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.dialogRef.close(this.form.value);
  }
}

@Component({
  selector: 'app-produtos',
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
  templateUrl: './produtos.component.html',
  styleUrls: ['./produtos.component.scss'],
})
export class ProdutosComponent implements OnInit {
  displayedColumns = ['nome', 'tipo', 'cnae', 'lc116', 'valor_padrao', 'active', 'actions'];
  produtos: any[] = [];
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
    this.api.get<any>('/produtor/produtos').subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.produtos = res.data.items ?? res.data;
        }
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Erro ao carregar produtos', 'Fechar', { duration: 3000 });
      },
    });
  }

  openDialog(produto?: any): void {
    const ref = this.dialog.open(ProdutoDialogComponent, {
      data: { produto: produto ?? null },
      width: '520px',
    });
    ref.afterClosed().subscribe((result) => {
      if (!result) return;
      if (produto) {
        this.api.put<any>(`/produtor/produtos/${produto.id}`, result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Produto atualizado!', 'Fechar', { duration: 3000 });
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
        this.api.post<any>('/produtor/produtos', result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Produto criado!', 'Fechar', { duration: 3000 });
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

  deleteProduto(produto: any): void {
    if (!confirm(`Remover "${produto.nome}"?`)) return;
    this.api.delete<any>(`/produtor/produtos/${produto.id}`).subscribe({
      next: (res) => {
        if (res.success) {
          this.snackBar.open('Produto removido!', 'Fechar', { duration: 3000 });
          this.load();
        } else {
          this.snackBar.open(res.message || 'Erro', 'Fechar', { duration: 3000 });
        }
      },
      error: () => {
        this.snackBar.open('Erro ao remover', 'Fechar', { duration: 3000 });
      },
    });
  }
}
