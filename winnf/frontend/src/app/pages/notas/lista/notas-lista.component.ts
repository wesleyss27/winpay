import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatChipsModule } from '@angular/material/chips';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatCardModule } from '@angular/material/card';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule } from '@angular/material/core';
import { MatTooltipModule } from '@angular/material/tooltip';
import { ApiService } from '../../../core/services/api.service';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-notas-lista',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatChipsModule,
    MatProgressSpinnerModule,
    MatCardModule,
    MatPaginatorModule,
    MatSnackBarModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatTooltipModule,
  ],
  templateUrl: './notas-lista.component.html',
  styleUrls: ['./notas-lista.component.scss'],
})
export class NotasListaComponent implements OnInit {
  displayedColumns = ['data', 'destinatario', 'tipo', 'status', 'valor', 'links', 'actions'];
  notas: any[] = [];
  isLoading = false;
  totalItems = 0;
  pageSize = 20;
  currentPage = 1;
  role: string | null = null;

  filterForm: FormGroup;

  statusOptions = [
    { value: '', label: 'Todos' },
    { value: 'pendente', label: 'Pendente' },
    { value: 'autorizada', label: 'Autorizada' },
    { value: 'cancelada', label: 'Cancelada' },
    { value: 'rejeitada', label: 'Rejeitada' },
    { value: 'denegada', label: 'Denegada' },
  ];

  consultingId: number | null = null;
  cancelingId: number | null = null;

  constructor(
    private api: ApiService,
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router,
    private snackBar: MatSnackBar
  ) {
    this.filterForm = this.fb.group({
      status: [''],
      date_from: [''],
      date_to: [''],
    });
  }

  ngOnInit(): void {
    this.role = this.authService.getRole();
    this.load();
  }

  load(): void {
    this.isLoading = true;
    const f = this.filterForm.value;
    const params: any = { page: this.currentPage, per_page: this.pageSize };
    if (f.status) params['status'] = f.status;
    if (f.date_from) params['date_from'] = this.formatDate(f.date_from);
    if (f.date_to) params['date_to'] = this.formatDate(f.date_to);

    this.api.get<any>('/notas', params).subscribe({
      next: (res) => {
        this.isLoading = false;
        if (res.success) {
          this.notas = res.data.items ?? res.data;
          this.totalItems = res.data.total ?? this.notas.length;
        }
      },
      error: () => {
        this.isLoading = false;
        this.snackBar.open('Erro ao carregar notas', 'Fechar', { duration: 3000 });
      },
    });
  }

  private formatDate(value: any): string {
    if (!value) return '';
    if (typeof value === 'string') return value;
    const d = new Date(value);
    return d.toISOString().split('T')[0];
  }

  applyFilters(): void {
    this.currentPage = 1;
    this.load();
  }

  clearFilters(): void {
    this.filterForm.reset({ status: '', date_from: '', date_to: '' });
    this.currentPage = 1;
    this.load();
  }

  onPageChange(event: PageEvent): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.load();
  }

  consultar(nota: any): void {
    this.consultingId = nota.id;
    this.api.post<any>(`/notas/${nota.id}/consultar`).subscribe({
      next: (res) => {
        this.consultingId = null;
        if (res.success) {
          this.snackBar.open('Nota consultada com sucesso!', 'Fechar', { duration: 3000 });
          this.load();
        } else {
          this.snackBar.open(res.message || 'Erro na consulta', 'Fechar', { duration: 3000 });
        }
      },
      error: (err) => {
        this.consultingId = null;
        this.snackBar.open(err?.error?.message || 'Erro na consulta', 'Fechar', { duration: 3000 });
      },
    });
  }

  cancelar(nota: any): void {
    if (!confirm(`Cancelar a nota fiscal #${nota.id}?`)) return;
    this.cancelingId = nota.id;
    this.api.post<any>(`/notas/${nota.id}/cancelar`).subscribe({
      next: (res) => {
        this.cancelingId = null;
        if (res.success) {
          this.snackBar.open('Nota cancelada!', 'Fechar', { duration: 3000 });
          this.load();
        } else {
          this.snackBar.open(res.message || 'Erro ao cancelar', 'Fechar', { duration: 3000 });
        }
      },
      error: (err) => {
        this.cancelingId = null;
        this.snackBar.open(err?.error?.message || 'Erro ao cancelar', 'Fechar', { duration: 3000 });
      },
    });
  }

  getStatusClass(status: string): string {
    const map: Record<string, string> = {
      pendente: 'status-pendente',
      autorizada: 'status-autorizada',
      cancelada: 'status-cancelada',
      rejeitada: 'status-rejeitada',
      denegada: 'status-denegada',
    };
    return map[status] ?? 'status-default';
  }

  goEmitir(): void {
    this.router.navigate(['/notas/emitir']);
  }

  goLote(): void {
    this.router.navigate(['/notas/lote']);
  }
}
