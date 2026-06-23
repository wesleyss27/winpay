import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../../servicos/api.service';

@Component({
  selector: 'app-cancelar',
  standalone: true,
  imports: [CommonModule],
  template: `
    <h2>Cancelamento</h2>

    <div class="card" *ngIf="!enviado">
      <p>Ao solicitar o cancelamento, nossa equipe entrará em contato em até 24 horas para confirmar e orientar os próximos passos.</p>
      <p style="color:#666; font-size:0.9rem">O acesso permanece ativo até o fim do período contratado.</p>
      <button (click)="confirmar = true" *ngIf="!confirmar">Solicitar Cancelamento</button>

      <div class="confirmacao" *ngIf="confirmar">
        <p style="color:#ef5350; font-weight:600">Tem certeza que deseja cancelar seu plano?</p>
        <div class="acoes">
          <button class="btn-secundario" (click)="confirmar = false">Voltar</button>
          <button class="btn-perigo" (click)="solicitar()" [disabled]="carregando">
            {{ carregando ? 'Enviando...' : 'Confirmar Cancelamento' }}
          </button>
        </div>
      </div>
    </div>

    <div class="card sucesso" *ngIf="enviado">
      <p>✓ Solicitação enviada! Nossa equipe entrará em contato em breve.</p>
    </div>
  `,
  styles: [`
    h2 { margin-bottom: 1.5rem; }
    .card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 1.5rem; max-width: 520px; }
    .card.sucesso { border-color: #1a3a1a; color: #4caf50; }
    p { line-height: 1.6; margin-bottom: 1rem; }
    button { background: #e53e3e; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .btn-secundario { background: #2a2a2a; color: #aaa; }
    .btn-perigo { background: #c53030; }
    .acoes { display: flex; gap: 0.75rem; margin-top: 1rem; }
    button:disabled { opacity: 0.6; }
  `]
})
export class CancelarComponent {
  confirmar = false;
  carregando = false;
  enviado = false;

  constructor(private api: ApiService) {}

  solicitar() {
    this.carregando = true;
    this.api.solicitarCancelamento().subscribe(() => {
      this.enviado = true;
      this.carregando = false;
    });
  }
}
