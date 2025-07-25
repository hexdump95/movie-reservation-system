import {CanActivateFn, Router} from '@angular/router';
import {inject} from "@angular/core";
import {AuthService} from "./auth.service";
import {catchError, map, of} from "rxjs";

export const authGuard: CanActivateFn = (route) => {
  const authService: AuthService = inject(AuthService);
  const router: Router = inject(Router);

  const requiredRoles: string[] = route.data?.['roles'] || [];

  return authService.validateToken().pipe(
    map(valor => {
      if (!valor.isValid) {
        void router.navigate(['/login']);
        return false;
      }

      if (requiredRoles.length === 0) {
        return true;
      }

      const payload = authService.getUserPayload();
      const userRoles: string[] = payload?.roles || [];

      const hasRequiredRole = requiredRoles.some(role =>
        userRoles.includes(role)
      );

      if (!hasRequiredRole) {
        void router.navigate(['/']);
        return false;
      }

      return true;
    }),
    catchError(_ => {
      void router.navigate(['/login']);
      return of(false);
    })
  );
};
