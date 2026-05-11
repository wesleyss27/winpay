import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { MatCardModule } from '@angular/material/card';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { ApiService } from '../../../core/services/api.service';

@Component({
  selector: 'app-integracao',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatSelectModule,
    MatCardModule,
    MatProgressSpinnerModule,
    MatIconModule,
    MatSnackBarModule,
  ],
  templateUrl: './integracao.component.html',
  styleUrls: ['./integracao.component.scss'],
})
export class IntegracaoComponent implements OnInit {
  form: FormGroup;
  isLoading = false;
  isSaving = false;
  hasConfig = false;
  configId: number | null = null;

  constructor(
    private fb: FormBuilder,
    private api: ApiService,
    private snackBar: MatSnackBar
  ) {
    this.form = this.fb.group({
      api_key: ['', Validators.required],
      cnae: [''],
      lc116: [''],
      enviar_email: ['nao'],
      cidade_padrao: [''],
      uf_padrao: ['', Validators.maxLength(2)],
      cep_padrao: ['', Validators.maxLength(8)],
    });
  }

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.isLoading = true;
    this.api.get<any>('/produtor/integracao').subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success && res.data) {
          const cfg = Array.isArray(res.data) ? res.data[0] : res.data;
          if (cfg) {
            this.hasConfig = true;
            this.configId = cfg.id;
            this.form.patchValue({
              api_key: cfg.api_key ?? '',
              cnae: cfg.cnae ?? '',
              lc116: cfg.lc116 ?? '',
              enviar_email: cfg.enviar_email ?? 'nao',
              cidade_padrao: cfg.cidade_padrao ?? '',
              uf_padrao: cfg.uf_padrao ?? '',
              cep_padrao: cfg.cep_padrao ?? '',
            });
          }
        }
      },
      error: () => {
        this.isLoading = false;
      },
    });
  }

  save(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    this.isSaving = true;
    const payload = this.form.value;

    const req = this.hasConfig && this.configId
      ? this.api.put<any>(`/produtor/integracao/${this.configId}`, payload)
      : this.api.post<any>('/produtor/integracao', payload);

    req.subscribe({
      next: (res) => {
        this.isSaving = false;
        if (res.success) {
          this.snackBar.open('Configuração salva com sucesso!', 'Fechar', { duration: 3000 });
          this.hasConfig = true;
          if (res.data?.id) this.configId = res.data.id;
        } else {
          this.snackBar.open(res.message || 'Erro ao salvar', 'Fechar', { duration: 3000 });
        }
      },
      error: (err) => {
        this.isSaving = false;
        this.snackBar.open(err?.error?.message || 'Erro ao salvar', 'Fechar', { duration: 3000 });
      },
    });
  }
}
