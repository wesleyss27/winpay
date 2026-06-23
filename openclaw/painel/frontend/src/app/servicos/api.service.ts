import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

const API = '/api';

@Injectable({ providedIn: 'root' })
export class ApiService {
  constructor(private http: HttpClient) {}

  login(email: string, senha: string): Observable<any> {
    const form = new FormData();
    form.append('username', email);
    form.append('password', senha);
    return this.http.post(`${API}/auth/login`, form);
  }

  perfil(): Observable<any> {
    return this.http.get(`${API}/auth/perfil`);
  }

  // Admin
  listarClientes(): Observable<any[]> {
    return this.http.get<any[]>(`${API}/admin/clientes`);
  }

  criarCliente(dados: any): Observable<any> {
    return this.http.post(`${API}/admin/clientes`, dados);
  }

  atualizarCliente(id: number, dados: any): Observable<any> {
    return this.http.put(`${API}/admin/clientes/${id}`, dados);
  }

  deletarCliente(id: number): Observable<any> {
    return this.http.delete(`${API}/admin/clientes/${id}`);
  }

  listarTicketsAdmin(): Observable<any[]> {
    return this.http.get<any[]>(`${API}/admin/tickets`);
  }

  // Cliente
  meuPlano(): Observable<any> {
    return this.http.get(`${API}/cliente/meu-plano`);
  }

  meuTickets(): Observable<any[]> {
    return this.http.get<any[]>(`${API}/cliente/tickets`);
  }

  abrirTicket(dados: any): Observable<any> {
    return this.http.post(`${API}/cliente/tickets`, dados);
  }

  solicitarCancelamento(): Observable<any> {
    return this.http.post(`${API}/cliente/cancelar`, {});
  }

  statusOpenclaw(): Observable<any> {
    return this.http.get(`${API}/status/openclaw`);
  }
}
