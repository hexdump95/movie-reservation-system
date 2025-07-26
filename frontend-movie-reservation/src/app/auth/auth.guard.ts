import {CanActivateFn, Router} from '@angular/router';
import {inject} from "@angular/core";
import {AuthService} from "./auth.service";
import {catchError, map, of} from "rxjs";

export const authGuard: CanActivateFn = (route) => {
  const authService: AuthService = inject(AuthService);
  const router: Router = inject(Router);

  const requiredRoles: string[] = route.data?.['roles'] || [];
  const requiredPermissions: string[] = route.data?.['permissions'] || [];

  return authService.validateToken().pipe(
    map(valor => {
      if (!valor.isValid) {
        void router.navigate(['/login']);
        return false;
      }

      if (requiredRoles.length === 0 && requiredPermissions.length === 0) {
        return true;
      }

      const payload = authService.getUserPayload();
      const userRoles: string[] = payload?.roles || [];
      const userPermissions: string[] = payload?.permissions || [];

      const hasRequiredRole = requiredRoles.length === 0
        ? true
        : requiredRoles.some(role =>
          userRoles.includes(role)
        );

      const hasRequiredPermission = requiredPermissions.length === 0
        ? true
        : requiredPermissions.some(permission =>
          userPermissions.includes(permission)
        );

      if (!hasRequiredRole || !hasRequiredPermission) {
        console.log(!hasRequiredRole);
        console.log(!hasRequiredPermission);
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
