import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../../servicos/api.service';

@Component({
  selector: 'app-clientes',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div class="cabecalho">
      <h2>Clientes</h2>
      <button class="btn-primario" (click)="exibirFormulario = true">+ Novo Cliente</button>
    </div>

    <div class="modal" *ngIf="exibirFormulario">
      <div class="modal-card">
        <h3>{{ editando ? 'Editar Cliente' : 'Novo Cliente' }}</h3>
        <form (ngSubmit)="salvar()">
          <div class="linha">
            <div class="campo">
              <label>Nome</label>
              <input [(ngModel)]="formulario.nome" name="nome" required />
            </div>
            <div class="campo">
              <label>Empresa</label>
              <input [(ngModel)]="formulario.nome_empresa" name="nome_empresa" required />
            </div>
          </div>
          <div class="linha">
            <div class="campo">
              <label>E-mail</label>
              <input type="email" [(ngModel)]="formulario.email" name="email" [disabled]="editando" />
            </div>
            <div class="campo">
              <label>Telefone</label>
              <input [(ngModel)]="formulario.telefone" name="telefone" />
            </div>
          </div>
          <div class="linha" *ngIf="!editando">
            <div class="campo">
              <label>Senha</label>
              <input type="password" [(ngModel)]="formulario.senha" name="senha" required />
            </div>
          </div>
          <div class="linha">
            <div class="campo">
              <label>Plano</label>
              <select [(ngModel)]="formulario.plano" name="plano">
                <option value="start">Start — R$99,90/mês</option>
                <option value="pro">Pro — R$197,90/mês</option>
                <option value="obsidian">Obsidian — R$397,90/mês</option>
              </select>
            </div>
            <div class="campo" *ngIf="editando">
              <label>Status</label>
              <select [(ngModel)]="formulario.status" name="status">
                <option value="ativo">Ativo</option>
                <option value="suspenso">Suspenso</option>
                <option value="cancelado">Cancelado</option>
              </select>
            </div>
          </div>
          <div class="acoes">
            <button type="button" class="btn-secundario" (click)="fecharFormulario()">Cancelar</button>
            <button type="submit" class="btn-primario">Salvar</button>
          </div>
        </form>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Empresa</th>
          <th>E-mail</th>
          <th>Plano</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr *ngFor="let c of clientes">
          <td>{{ c.nome }}</td>
          <td>{{ c.nome_empresa }}</td>
          <td>{{ c.email }}</td>
          <td><span class="badge plano-{{c.plano}}">{{ c.plano | uppercase }}</span></td>
          <td><span class="badge status-{{c.status}}">{{ c.status }}</span></td>
          <td>
            <button class="btn-icone" (click)="editar(c)">✏️</button>
            <button class="btn-icone" (click)="deletar(c.id)">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>
  `,
  styles: [`
    .cabecalho { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    h2 { margin: 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid #2a2a2a; font-size: 0.9rem; }
    th { color: #666; font-weight: 500; }
    .badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .plano-start { background: #1a3a1a; color: #4caf50; }
    .plano-pro { background: #1a2a3a; color: #42a5f5; }
    .plano-obsidian { background: #2a1a3a; color: #ab47bc; }
    .status-ativo { background: #1a3a1a; color: #4caf50; }
    .status-suspenso { background: #3a2a1a; color: #ffa726; }
    .status-cancelado { background: #3a1a1a; color: #ef5350; }
    .btn-primario { background: #e53e3e; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .btn-secundario { background: #2a2a2a; color: #aaa; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; }
    .btn-icone { background: none; border: none; cursor: pointer; font-size: 1rem; }
    .modal { position: fixed; inset: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 100; }
    .modal-card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 12px; padding: 2rem; width: 560px; }
    h3 { margin: 0 0 1.5rem; }
    .linha { display: flex; gap: 1rem; margin-bottom: 1rem; }
    .campo { flex: 1; }
    label { display: block; color: #aaa; font-size: 0.8rem; margin-bottom: 0.3rem; }
    input, select { width: 100%; padding: 0.7rem; background: #111; border: 1px solid #333; border-radius: 8px; color: #fff; box-sizing: border-box; }
    .acoes { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; }
  `]
})
export class ClientesComponent implements OnInit {
  clientes: any[] = [];
  exibirFormulario = false;
  editando: any = null;
  formulario: any = { plano: 'start' };

  constructor(private api: ApiService) {}

  ngOnInit() { this.carregar(); }

  carregar() {
    this.api.listarClientes().subscribe(data => this.clientes = data);
  }

  editar(cliente: any) {
    this.editando = cliente;
    this.formulario = { ...cliente };
    this.exibirFormulario = true;
  }

  fecharFormulario() {
    this.exibirFormulario = false;
    this.editando = null;
    this.formulario = { plano: 'start' };
  }

  salvar() {
    if (this.editando) {
      this.api.atualizarCliente(this.editando.id, this.formulario).subscribe(() => {
        this.carregar();
        this.fecharFormulario();
      });
    } else {
      this.api.criarCliente(this.formulario).subscribe(() => {
        this.carregar();
        this.fecharFormulario();
      });
    }
  }

  deletar(id: number) {
    if (confirm('Remover este cliente?')) {
      this.api.deletarCliente(id).subscribe(() => this.carregar());
    }
  }
}
