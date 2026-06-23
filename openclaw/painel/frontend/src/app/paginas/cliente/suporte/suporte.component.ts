import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../../servicos/api.service';

@Component({
  selector: 'app-suporte',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <h2>Suporte</h2>

    <div class="formulario">
      <h3>Abrir novo ticket</h3>
      <div class="campo">
        <label>Assunto</label>
        <input [(ngModel)]="assunto" placeholder="Descreva brevemente o problema" />
      </div>
      <div class="campo">
        <label>Mensagem</label>
        <textarea [(ngModel)]="mensagem" rows="5" placeholder="Detalhe sua solicitação..."></textarea>
      </div>
      <button (click)="enviar()" [disabled]="carregando">
        {{ carregando ? 'Enviando...' : 'Enviar Ticket' }}
      </button>
      <div class="sucesso" *ngIf="sucesso">✓ Ticket enviado com sucesso!</div>
    </div>

    <h3 style="margin-top: 2rem">Meus tickets</h3>
    <table *ngIf="tickets.length > 0">
      <thead>
        <tr><th>Assunto</th><th>Status</th><th>Data</th></tr>
      </thead>
      <tbody>
        <tr *ngFor="let t of tickets">
          <td>{{ t.assunto }}</td>
          <td><span class="badge status-{{t.status}}">{{ t.status }}</span></td>
          <td>{{ t.criado_em | date:'dd/MM/yyyy HH:mm' }}</td>
        </tr>
      </tbody>
    </table>
    <p *ngIf="tickets.length === 0" style="color:#666">Nenhum ticket aberto ainda.</p>
  `,
  styles: [`
    h2, h3 { margin-bottom: 1rem; }
    .formulario { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 1.5rem; max-width: 600px; }
    .campo { margin-bottom: 1rem; }
    label { display: block; color: #aaa; font-size: 0.8rem; margin-bottom: 0.3rem; }
    input, textarea {
      width: 100%; padding: 0.75rem; background: #111;
      border: 1px solid #333; border-radius: 8px; color: #fff;
      font-size: 0.9rem; box-sizing: border-box; resize: vertical;
    }
    button { background: #e53e3e; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600; }
    button:disabled { opacity: 0.6; }
    .sucesso { color: #4caf50; margin-top: 0.75rem; font-size: 0.9rem; }
    table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
    th, td { padding: 0.75rem 1rem; border-bottom: 1px solid #2a2a2a; text-align: left; font-size: 0.9rem; }
    th { color: #666; }
    .badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-aberto { background: #1a2a3a; color: #42a5f5; }
    .status-fechado { background: #1a3a1a; color: #4caf50; }
  `]
})
export class SuporteComponent implements OnInit {
  tickets: any[] = [];
  assunto = '';
  mensagem = '';
  carregando = false;
  sucesso = false;

  constructor(private api: ApiService) {}

  ngOnInit() {
    this.api.meuTickets().subscribe(d => this.tickets = d);
  }

  enviar() {
    if (!this.assunto || !this.mensagem) return;
    this.carregando = true;
    this.api.abrirTicket({ assunto: this.assunto, mensagem: this.mensagem }).subscribe(() => {
      this.sucesso = true;
      this.assunto = '';
      this.mensagem = '';
      this.carregando = false;
      this.api.meuTickets().subscribe(d => this.tickets = d);
      setTimeout(() => this.sucesso = false, 3000);
    });
  }
}
