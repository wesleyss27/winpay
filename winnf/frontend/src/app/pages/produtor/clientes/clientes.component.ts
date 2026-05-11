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
import { debounceTime, distinctUntilChanged, Subject } from 'rxjs';
import { ApiService } from '../../../core/services/api.service';

@Component({
  selector: 'app-cliente-dialog',
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
    <h2 mat-dialog-title>{{ data.cliente ? 'Editar Cliente' : 'Novo Cliente' }}</h2>
    <mat-dialog-content class="dialog-content">
      <form [formGroup]="form" novalidate class="dialog-form">
        <div class="form-row">
          <mat-form-field appearance="outline" class="full-width">
            <mat-label>Nome</mat-label>
            <input matInput formControlName="nome" />
            <mat-error *ngIf="form.get('nome')?.hasError('required')">Nome é obrigatório</mat-error>
          </mat-form-field>
        </div>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>CPF/CNPJ</mat-label>
            <input matInput formControlName="cpf_cnpj" />
            <mat-error *ngIf="form.get('cpf_cnpj')?.hasError('required')">CPF/CNPJ é obrigatório</mat-error>
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Tipo</mat-label>
            <mat-select formControlName="tipo">
              <mat-option value="F">Pessoa Física</mat-option>
              <mat-option value="J">Pessoa Jurídica</mat-option>
              <mat-option value="E">Estrangeiro</mat-option>
            </mat-select>
          </mat-form-field>
        </div>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>E-mail</mat-label>
            <input matInput type="email" formControlName="email" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Telefone</mat-label>
            <input matInput formControlName="telefone" />
          </mat-form-field>
        </div>
        <div class="section-label">Endereço</div>
        <div class="form-row">
          <mat-form-field appearance="outline" class="flex-grow">
            <mat-label>Rua</mat-label>
            <input matInput formControlName="rua" />
          </mat-form-field>
          <mat-form-field appearance="outline" class="field-num">
            <mat-label>Número</mat-label>
            <input matInput formControlName="numero" />
          </mat-form-field>
        </div>
        <div class="form-row">
          <mat-form-field appearance="outline">
            <mat-label>Complemento</mat-label>
            <input matInput formControlName="complemento" />
          </mat-form-field>
          <mat-form-field appearance="outline">
            <mat-label>Bairro</mat-label>
            <input matInput formControlName="bairro" />
          </mat-form-field>
        </div>
        <div class="form-row">
          <mat-form-field appearance="outline" class="flex-grow">
            <mat-label>Cidade</mat-label>
            <input matInput formControlName="cidade" />
          </mat-form-field>
          <mat-form-field appearance="outline" class="field-uf">
            <mat-label>UF</mat-label>
            <input matInput formControlName="uf" maxlength="2" />
          </mat-form-field>
          <mat-form-field appearance="outline" class="field-cep">
            <mat-label>CEP</mat-label>
            <input matInput formControlName="cep" maxlength="8" />
          </mat-form-field>
        </div>
      </form>
    </mat-dialog-content>
    <mat-dialog-actions align="end">
      <button mat-button mat-dialog-close>Cancelar</button>
      <button mat-raised-button color="primary" (click)="save()">Salvar</button>
    </mat-dialog-actions>
  `,
  styles: [`
    .dialog-content { max-height: 70vh; overflow-y: auto; }
    .dialog-form { display: flex; flex-direction: column; gap: 4px; min-width: 480px; padding-top: 8px; }
    .form-row { display: flex; gap: 12px; }
    .full-width { width: 100%; }
    .flex-grow { flex: 2; }
    .field-num { width: 100px; flex: 0 0 100px; }
    .field-uf { width: 70px; flex: 0 0 70px; }
    .field-cep { width: 130px; flex: 0 0 130px; }
    mat-form-field { flex: 1; }
    .section-label { font-size: 13px; font-weight: 600; color: #1a237e; margin: 8px 0 4px; }
  `],
})
export class ClienteDialogComponent {
  form: FormGroup;

  constructor(
    private fb: FormBuilder,
    public dialogRef: MatDialogRef<ClienteDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: { cliente: any }
  ) {
    const c = data.cliente;
    this.form = this.fb.group({
      nome: [c?.nome ?? '', Validators.required],
      cpf_cnpj: [c?.cpf_cnpj ?? '', Validators.required],
      tipo: [c?.tipo ?? 'F'],
      email: [c?.email ?? ''],
      telefone: [c?.telefone ?? ''],
      rua: [c?.rua ?? ''],
      numero: [c?.numero ?? ''],
      complemento: [c?.complemento ?? ''],
      bairro: [c?.bairro ?? ''],
      cidade: [c?.cidade ?? ''],
      uf: [c?.uf ?? ''],
      cep: [c?.cep ?? ''],
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
  selector: 'app-clientes',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatProgressSpinnerModule,
    MatCardModule,
    MatTooltipModule,
    MatSnackBarModule,
    MatDialogModule,
  ],
  templateUrl: './clientes.component.html',
  styleUrls: ['./clientes.component.scss'],
})
export class ClientesComponent implements OnInit {
  displayedColumns = ['nome', 'cpf_cnpj', 'cidade_uf', 'email', 'actions'];
  clientes: any[] = [];
  isLoading = false;
  searchTerm = '';
  private searchSubject = new Subject<string>();

  constructor(
    private api: ApiService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar
  ) {}

  ngOnInit(): void {
    this.load();
    this.searchSubject.pipe(debounceTime(400), distinctUntilChanged()).subscribe((term) => {
      this.searchTerm = term;
      this.load();
    });
  }

  load(): void {
    this.isLoading = true;
    const params: any = {};
    if (this.searchTerm) params['search'] = this.searchTerm;
    this.api.get<any>('/produtor/clientes', params).subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.clientes = res.data.items ?? res.data;
        }
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Erro ao carregar clientes', 'Fechar', { duration: 3000 });
      },
    });
  }

  onSearch(event: Event): void {
    const value = (event.target as HTMLInputElement).value;
    this.searchSubject.next(value);
  }

  openDialog(cliente?: any): void {
    const ref = this.dialog.open(ClienteDialogComponent, {
      data: { cliente: cliente ?? null },
      width: '600px',
    });
    ref.afterClosed().subscribe((result) => {
      if (!result) return;
      if (cliente) {
        this.api.put<any>(`/produtor/clientes/${cliente.id}`, result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Cliente atualizado!', 'Fechar', { duration: 3000 });
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
        this.api.post<any>('/produtor/clientes', result).subscribe({
          next: (res) => {
            if (res.success) {
              this.snackBar.open('Cliente criado!', 'Fechar', { duration: 3000 });
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

  deleteCliente(cliente: any): void {
    if (!confirm(`Remover cliente "${cliente.nome}"?`)) return;
    this.api.delete<any>(`/produtor/clientes/${cliente.id}`).subscribe({
      next: (res) => {
        if (res.success) {
          this.snackBar.open('Cliente removido!', 'Fechar', { duration: 3000 });
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
