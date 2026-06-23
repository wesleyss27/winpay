import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../../servicos/api.service';

const PRECOS: Record<string, number> = { start: 99.90, pro: 197.90, obsidian: 397.90 };
const NOMES: Record<string, string> = { start: 'Start', pro: 'Pro', obsidian: 'Obsidian' };

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  template: `
    <h2>Dashboard</h2>

    <div class="cards">
      <div class="card">
        <div class="card-titulo">Plano Atual</div>
        <div class="card-valor plano-{{plano?.plano}}">{{ nomes[plano?.plano] || '—' }}</div>
        <div class="card-sub">{{ plano?.status | titlecase }}</div>
      </div>

      <div class="card">
        <div class="card-titulo">Mensalidade</div>
        <div class="card-valor">R$ {{ (precos[plano?.plano] || 0) | number:'1.2-2' }}</div>
        <div class="card-sub">por mês</div>
      </div>

      <div class="card">
        <div class="card-titulo">Status OpenClaw</div>
        <div class="card-valor" [class.online]="openclaw?.online" [class.offline]="!openclaw?.online">
          {{ openclaw?.online ? '● Online' : '○ Offline' }}
        </div>
        <div class="card-sub">{{ openclaw?.status }}</div>
      </div>

      <div class="card">
        <div class="card-titulo">Cliente desde</div>
        <div class="card-valor">{{ plano?.data_inicio | date:'MM/yyyy' }}</div>
        <div class="card-sub">{{ plano?.data_inicio | date:'dd/MM/yyyy' }}</div>
      </div>
    </div>
  `,
  styles: [`
    h2 { margin-bottom: 1.5rem; }
    .cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
    .card {
      background: #1a1a1a; border: 1px solid #2a2a2a;
      border-radius: 12px; padding: 1.5rem;
    }
    .card-titulo { color: #666; font-size: 0.8rem; margin-bottom: 0.5rem; }
    .card-valor { font-size: 1.5rem; font-weight: 700; color: #fff; }
    .card-sub { color: #555; font-size: 0.8rem; margin-top: 0.25rem; }
    .plano-start { color: #4caf50 !important; }
    .plano-pro { color: #42a5f5 !important; }
    .plano-obsidian { color: #ab47bc !important; }
    .online { color: #4caf50 !important; }
    .offline { color: #ef5350 !important; }
  `]
})
export class DashboardComponent implements OnInit {
  plano: any = null;
  openclaw: any = null;
  precos = PRECOS;
  nomes = NOMES;

  constructor(private api: ApiService) {}

  ngOnInit() {
    this.api.meuPlano().subscribe(d => this.plano = d);
    this.api.statusOpenclaw().subscribe(d => this.openclaw = d);
  }
}
