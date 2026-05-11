import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup } from '@angular/forms';
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
import { ApiService } from '../../../core/services/api.service';

@Component({
  selector: 'app-admin-notas',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
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
  ],
  templateUrl: './admin-notas.component.html',
  styleUrls: ['./admin-notas.component.scss'],
})
export class AdminNotasComponent implements OnInit {
  displayedColumns = ['emitente', 'produtor', 'tipo', 'status', 'valor', 'data'];
  notas: any[] = [];
  isLoading = false;
  totalItems = 0;
  pageSize = 20;
  currentPage = 1;

  filterForm: FormGroup;

  statusOptions = [
    { value: '', label: 'Todos' },
    { value: 'pendente', label: 'Pendente' },
    { value: 'autorizada', label: 'Autorizada' },
    { value: 'cancelada', label: 'Cancelada' },
    { value: 'rejeitada', label: 'Rejeitada' },
    { value: 'denegada', label: 'Denegada' },
  ];

  constructor(
    private api: ApiService,
    private fb: FormBuilder,
    private snackBar: MatSnackBar
  ) {
    this.filterForm = this.fb.group({
      status: [''],
      date_from: [''],
      date_to: [''],
      search: [''],
    });
  }

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.isLoading = true;
    const f = this.filterForm.value;
    const params: any = {
      page: this.currentPage,
      per_page: this.pageSize,
    };
    if (f.status) params['status'] = f.status;
    if (f.date_from) params['date_from'] = this.formatDate(f.date_from);
    if (f.date_to) params['date_to'] = this.formatDate(f.date_to);
    if (f.search) params['search'] = f.search;

    this.api.get<any>('/admin/notas', params).subscribe({
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
    this.filterForm.reset({ status: '', date_from: '', date_to: '', search: '' });
    this.currentPage = 1;
    this.load();
  }

  onPageChange(event: PageEvent): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.load();
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
}
