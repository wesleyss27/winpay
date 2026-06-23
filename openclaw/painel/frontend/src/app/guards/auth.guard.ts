import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';
import { AuthService } from '../servicos/auth.service';

export function guardaAuth(papelExigido: string): CanActivateFn {
  return () => {
    const auth = inject(AuthService);
    const router = inject(Router);
    if (!auth.estaLogado()) {
      router.navigate(['/login']);
      return false;
    }
    if (auth.obterPapel() !== papelExigido) {
      const destino = auth.obterPapel() === 'admin' ? '/admin' : '/painel';
      router.navigate([destino]);
      return false;
    }
    return true;
  };
}
