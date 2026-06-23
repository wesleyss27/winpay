import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../../servicos/api.service';

@Component({
  selector: 'app-tickets',
  standalone: true,
  imports: [CommonModule],
  template: `
    <h2>Tickets de Suporte</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Assunto</th>
          <th>Mensagem</th>
          <th>Status</th>
          <th>Data</th>
        </tr>
      </thead>
      <tbody>
        <tr *ngFor="let t of tickets">
          <td>#{{ t.id }}</td>
          <td>{{ t.assunto }}</td>
          <td>{{ t.mensagem }}</td>
          <td><span class="badge status-{{t.status}}">{{ t.status }}</span></td>
          <td>{{ t.criado_em | date:'dd/MM/yyyy HH:mm' }}</td>
        </tr>
      </tbody>
    </table>
    <p *ngIf="tickets.length === 0" style="color:#666">Nenhum ticket aberto.</p>
  `,
  styles: [`
    h2 { margin-bottom: 1.5rem; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid #2a2a2a; font-size: 0.9rem; }
    th { color: #666; font-weight: 500; }
    .badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-aberto { background: #1a2a3a; color: #42a5f5; }
    .status-fechado { background: #1a3a1a; color: #4caf50; }
  `]
})
export class TicketsComponent implements OnInit {
  tickets: any[] = [];

  constructor(private api: ApiService) {}

  ngOnInit() {
    this.api.listarTicketsAdmin().subscribe(data => this.tickets = data);
  }
}
