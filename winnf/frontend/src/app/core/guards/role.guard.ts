import { inject } from '@angular/core';
import { CanActivateFn, Router, ActivatedRouteSnapshot } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { UserRole } from '../models/user.model';

export const roleGuard: CanActivateFn = (route: ActivatedRouteSnapshot) => {
  const auth = inject(AuthService);
  const router = inject(Router);
  const allowed: UserRole[] = route.data['roles'] ?? [];
  const role = auth.getRole();
  if (role && allowed.includes(role)) return true;
  router.navigate(['/dashboard']);
  return false;
};
