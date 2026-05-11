import { Component, OnInit } from '@angular/core';
import { CommonModule, DecimalPipe } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatCardModule } from '@angular/material/card';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatTableModule } from '@angular/material/table';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MatDividerModule } from '@angular/material/divider';
import { debounceTime, Subject } from 'rxjs';
import { ApiService } from '../../../core/services/api.service';

export interface Destinatario {
  nome: string;
  cpf_cnpj: string;
  tipo: string;
  email: string;
  status?: 'pendente' | 'sucesso' | 'erro';
  statusMsg?: string;
}

interface Produto {
  id: number;
  nome: string;
  tipo: string;
  cnae: string | null;
  lc116: string | null;
  valor_padrao: number | null;
}

@Component({
  selector: 'app-lote',
  standalone: true,
  imports: [
    CommonModule,
    DecimalPipe,
    ReactiveFormsModule,
    RouterModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatCardModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
    MatTableModule,
    MatTooltipModule,
    MatDividerModule,
  ],
  templateUrl: './lote.component.html',
  styleUrls: ['./lote.component.scss'],
})
export class LoteComponent implements OnInit {
  notaForm: FormGroup;
  addForm: FormGroup;
  destinatarios: Destinatario[] = [];
  displayedColumns = ['nome', 'cpf_cnpj', 'tipo', 'email', 'status', 'actions'];

  catalogo: Produto[] = [];
  isSubmitting = false;
  isSearching = false;
  searchResults: any[] = [];
  private searchSubject = new Subject<string>();

  constructor(
    private fb: FormBuilder,
    private api: ApiService,
    private snackBar: MatSnackBar
  ) {
    this.notaForm = this.fb.group({
      tipo: ['nfse', Validators.required],
      descricao: ['', Validators.required],
      valor: ['', [Validators.required, Validators.min(0.01)]],
      competencia: ['', Validators.required],
      cnae: [''],
      lc116: [''],
    });

    this.addForm = this.fb.group({
      search: [''],
      nome: ['', Validators.required],
      cpf_cnpj: ['', Validators.required],
      tipo: ['F'],
      email: [''],
    });
  }

  ngOnInit(): void {
    this.loadCatalogo();

    this.searchSubject.pipe(debounceTime(400)).subscribe((term) => {
      if (term.length >= 3) {
        this.doSearch(term);
      } else {
        this.searchResults = [];
      }
    });
  }

  get tipoNota(): string {
    return this.notaForm.get('tipo')?.value ?? 'nfse';
  }

  get catalogoFiltrado(): Produto[] {
    const tipo = this.tipoNota === 'nfe' ? 'produto' : 'servico';
    return this.catalogo.filter(p => p.tipo === tipo);
  }

  private loadCatalogo(): void {
    this.api.get<any>('/notas/catalogo').subscribe({
      next: (res) => { if (res.success) this.catalogo = res.data; },
      error: () => {},
    });
  }

  onSelectCatalogo(produto: Produto | null): void {
    if (!produto) return;
    this.notaForm.patchValue({
      descricao: produto.nome,
      cnae: produto.cnae ?? '',
      lc116: produto.lc116 ?? '',
      valor: produto.valor_padrao ?? '',
    });
  }

  onSearchInput(event: Event): void {
    const value = (event.target as HTMLInputElement).value;
    this.searchSubject.next(value);
  }

  doSearch(term: string): void {
    this.isSearching = true;
    this.api.get<any>('/notas/clientes', { search: term }).subscribe({
      next: (res) => {
        this.isSearching = false;
        if (res.success) {
          this.searchResults = res.data.items ?? res.data;
        }
      },
      error: () => { this.isSearching = false; },
    });
  }

  addFromSearch(c: any): void {
    const existing = this.destinatarios.find((d) => d.cpf_cnpj === c.cpf_cnpj);
    if (existing) {
      this.snackBar.open('Destinatário já adicionado', 'Fechar', { duration: 2000 });
      return;
    }
    this.destinatarios = [...this.destinatarios, {
      nome: c.nome,
      cpf_cnpj: c.cpf_cnpj,
      tipo: c.tipo ?? 'F',
      email: c.email ?? '',
      status: 'pendente',
    }];
    this.searchResults = [];
  }

  addManual(): void {
    if (this.addForm.invalid) {
      this.addForm.markAllAsTouched();
      return;
    }
    const v = this.addForm.value;
    const existing = this.destinatarios.find((d) => d.cpf_cnpj === v.cpf_cnpj);
    if (existing) {
      this.snackBar.open('Destinatário já adicionado', 'Fechar', { duration: 2000 });
      return;
    }
    this.destinatarios = [...this.destinatarios, {
      nome: v.nome,
      cpf_cnpj: v.cpf_cnpj,
      tipo: v.tipo,
      email: v.email,
      status: 'pendente',
    }];
    this.addForm.reset({ tipo: 'F' });
  }

  removeDestinatario(index: number): void {
    this.destinatarios = this.destinatarios.filter((_, i) => i !== index);
  }

  emitirLote(): void {
    if (this.notaForm.invalid) {
      this.notaForm.markAllAsTouched();
      this.snackBar.open('Preencha os dados da nota antes de emitir', 'Fechar', { duration: 3000 });
      return;
    }

    if (this.destinatarios.length === 0) {
      this.snackBar.open('Adicione ao menos um destinatário', 'Fechar', { duration: 3000 });
      return;
    }

    this.isSubmitting = true;
    const v = this.notaForm.value;

    // Backend expects { notas: [ { tipo, destinatario, valor, descricao, ... } ] }
    const payload = {
      notas: this.destinatarios.map((d) => ({
        tipo: v.tipo,
        destinatario: { nome: d.nome, cpf_cnpj: d.cpf_cnpj, tipo: d.tipo, email: d.email },
        valor: v.valor,
        descricao: v.descricao,
        competencia: v.competencia,
        cnae: v.cnae,
        lc116: v.lc116,
      })),
    };

    this.api.post<any>('/notas/lote', payload).subscribe({
      next: (res) => {
        this.isSubmitting = false;
        const results: any[] = res.data ?? [];
        this.destinatarios = this.destinatarios.map((d, i) => ({
          ...d,
          status: results[i]?.success ? 'sucesso' : 'erro',
          statusMsg: results[i]?.message ?? '',
        }));
        const ok = results.filter(r => r.success).length;
        this.snackBar.open(`${ok}/${results.length} notas emitidas`, 'Fechar', { duration: 4000 });
      },
      error: (err) => {
        this.isSubmitting = false;
        this.snackBar.open(err?.error?.message || 'Erro ao emitir lote', 'Fechar', { duration: 3000 });
      },
    });
  }

  getStatusClass(status?: string): string {
    const map: Record<string, string> = {
      pendente: 'badge-pendente',
      sucesso: 'badge-sucesso',
      erro: 'badge-erro',
    };
    return map[status ?? 'pendente'] ?? 'badge-pendente';
  }
}
