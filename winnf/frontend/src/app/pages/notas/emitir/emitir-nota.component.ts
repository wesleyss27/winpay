import { Component, OnInit } from '@angular/core';
import { CommonModule, DecimalPipe } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatRadioModule } from '@angular/material/radio';
import { MatCardModule } from '@angular/material/card';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDividerModule } from '@angular/material/divider';
import { debounceTime, Subject } from 'rxjs';
import { ApiService } from '../../../core/services/api.service';

interface Produto {
  id: number;
  nome: string;
  tipo: string;
  cnae: string | null;
  lc116: string | null;
  valor_padrao: number | null;
}

@Component({
  selector: 'app-emitir-nota',
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
    MatRadioModule,
    MatCardModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
    MatDividerModule,
  ],
  templateUrl: './emitir-nota.component.html',
  styleUrls: ['./emitir-nota.component.scss'],
})
export class EmitirNotaComponent implements OnInit {
  form: FormGroup;
  isSubmitting = false;
  isSearchingClient = false;
  isFetchingCep = false;
  result: { success: boolean; notazz_id?: string; message?: string } | null = null;

  catalogo: Produto[] = [];

  private searchSubject = new Subject<string>();

  constructor(
    private fb: FormBuilder,
    private api: ApiService,
    private http: HttpClient,
    private snackBar: MatSnackBar
  ) {
    this.form = this.fb.group({
      tipo: ['nfse', Validators.required],
      // Destinatário
      nome: ['', Validators.required],
      cpf_cnpj: ['', Validators.required],
      tipo_pessoa: ['F', Validators.required],
      email: [''],
      telefone: [''],
      rua: [''],
      numero: [''],
      complemento: [''],
      bairro: [''],
      cidade: [''],
      uf: ['', Validators.maxLength(2)],
      cep: ['', Validators.maxLength(8)],
      search_cpf: [''],
      // Nota
      valor: ['', [Validators.required, Validators.min(0.01)]],
      descricao: ['', Validators.required],
      competencia: ['', [Validators.required, Validators.pattern(/^\d{2}\/\d{4}$/)]],
      cnae: [''],
      lc116: [''],
      // Produto (NF-e)
      produto_nome: [''],
      produto_quantidade: [1],
      produto_valor_unitario: [''],
    });
  }

  ngOnInit(): void {
    this.loadCatalogo();

    this.searchSubject.pipe(debounceTime(500)).subscribe((cpf) => {
      if (cpf.length >= 11) {
        this.buscarCliente(cpf);
      }
    });

    this.form.get('tipo')?.valueChanges.subscribe((tipo) => {
      this.updateNfeValidators(tipo);
    });
  }

  get tipoNota(): string {
    return this.form.get('tipo')?.value;
  }

  get catalogoFiltrado(): Produto[] {
    const tipo = this.tipoNota === 'nfe' ? 'produto' : 'servico';
    return this.catalogo.filter(p => p.tipo === tipo);
  }

  private loadCatalogo(): void {
    this.api.get<any>('/notas/catalogo').subscribe({
      next: (res) => {
        if (res.success) this.catalogo = res.data;
      },
      error: () => {},
    });
  }

  onSelectCatalogo(produto: Produto): void {
    if (!produto) return;
    this.form.patchValue({
      descricao: produto.nome,
      cnae: produto.cnae ?? '',
      lc116: produto.lc116 ?? '',
      valor: produto.valor_padrao ?? '',
      produto_nome: produto.nome,
    });
  }

  private updateNfeValidators(tipo: string): void {
    const produtoNome = this.form.get('produto_nome');
    const produtoQtd = this.form.get('produto_quantidade');
    const produtoValor = this.form.get('produto_valor_unitario');
    if (tipo === 'nfe') {
      produtoNome?.setValidators(Validators.required);
      produtoQtd?.setValidators([Validators.required, Validators.min(1)]);
      produtoValor?.setValidators([Validators.required, Validators.min(0.01)]);
    } else {
      produtoNome?.clearValidators();
      produtoQtd?.clearValidators();
      produtoValor?.clearValidators();
    }
    produtoNome?.updateValueAndValidity();
    produtoQtd?.updateValueAndValidity();
    produtoValor?.updateValueAndValidity();
  }

  onCepInput(event: Event): void {
    const cep = (event.target as HTMLInputElement).value.replace(/\D/g, '');
    if (cep.length === 8) this.buscarCep(cep);
  }

  buscarCep(cep: string): void {
    this.isFetchingCep = true;
    this.http.get<any>(`https://viacep.com.br/ws/${cep}/json/`).subscribe({
      next: (data) => {
        this.isFetchingCep = false;
        if (!data.erro) {
          this.form.patchValue({
            rua: data.logradouro ?? '',
            bairro: data.bairro ?? '',
            cidade: data.localidade ?? '',
            uf: data.uf ?? '',
          });
          this.snackBar.open('Endereço preenchido automaticamente', 'Fechar', { duration: 2000 });
        }
      },
      error: () => { this.isFetchingCep = false; },
    });
  }

  onCpfInput(event: Event): void {
    const value = (event.target as HTMLInputElement).value;
    this.searchSubject.next(value);
  }

  buscarCliente(cpf: string): void {
    this.isSearchingClient = true;
    this.api.get<any>('/notas/clientes', { search: cpf }).subscribe({
      next: (res) => {
        this.isSearchingClient = false;
        if (res.success) {
          const lista = res.data.items ?? res.data;
          if (lista.length > 0) {
            const c = lista[0];
            this.form.patchValue({
              nome: c.nome ?? '',
              cpf_cnpj: c.cpf_cnpj ?? '',
              tipo_pessoa: c.tipo ?? 'F',
              email: c.email ?? '',
              telefone: c.telefone ?? '',
              rua: c.rua ?? '',
              numero: c.numero ?? '',
              complemento: c.complemento ?? '',
              bairro: c.bairro ?? '',
              cidade: c.cidade ?? '',
              uf: c.uf ?? '',
              cep: c.cep ?? '',
            });
            this.snackBar.open('Cliente encontrado!', 'Fechar', { duration: 2000 });
          } else {
            this.snackBar.open('Cliente não encontrado', 'Fechar', { duration: 2000 });
          }
        }
      },
      error: () => {
        this.isSearchingClient = false;
        this.snackBar.open('Erro ao buscar cliente', 'Fechar', { duration: 2000 });
      },
    });
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      this.snackBar.open('Preencha todos os campos obrigatórios', 'Fechar', { duration: 3000 });
      return;
    }

    this.isSubmitting = true;
    this.result = null;

    const v = this.form.value;

    // Converte MM/AAAA → YYYY-MM-DD para a API
    let competencia = v.competencia ?? '';
    if (/^\d{2}\/\d{4}$/.test(competencia)) {
      const [mm, aaaa] = competencia.split('/');
      competencia = `${aaaa}-${mm}-01`;
    }

    const payload: any = {
      tipo: v.tipo,
      destinatario: {
        nome: v.nome,
        cpf_cnpj: v.cpf_cnpj,
        tipo: v.tipo_pessoa,
        email: v.email,
        telefone: v.telefone,
        rua: v.rua,
        numero: v.numero,
        complemento: v.complemento,
        bairro: v.bairro,
        cidade: v.cidade,
        uf: v.uf,
        cep: v.cep,
      },
      valor: v.valor,
      descricao: v.descricao,
      competencia,
      cnae: v.cnae,
      lc116: v.lc116,
    };

    if (v.tipo === 'nfe') {
      payload.produto = {
        nome: v.produto_nome,
        quantidade: v.produto_quantidade,
        valor_unitario: v.produto_valor_unitario,
      };
    }

    this.api.post<any>('/notas/emitir', payload).subscribe({
      next: (res) => {
        this.isSubmitting = false;
        if (res.success) {
          this.result = { success: true, notazz_id: res.data?.notazz_id ?? res.data?.id };
          this.form.reset({ tipo: 'nfse', tipo_pessoa: 'F', produto_quantidade: 1 });
        } else {
          this.result = { success: false, message: res.message || 'Erro ao emitir nota' };
        }
      },
      error: (err) => {
        this.isSubmitting = false;
        this.result = { success: false, message: err?.error?.message || 'Erro ao emitir nota' };
      },
    });
  }
}
