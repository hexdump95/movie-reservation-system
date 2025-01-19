import {Router} from '@angular/router';
import {inject} from "@angular/core";
import {AuthService} from "./auth.service";
import {Subscription} from "rxjs";


export const authGuard: () =>
  Subscription = (): Subscription => {
  const authService: AuthService = inject(AuthService);
  const router: Router = inject(Router);

  return authService.validateToken().subscribe({
    next: valor => {
      if (valor.isValid) {
        return true;
      } else {
        void router.navigate(['/login']);
        return false
      }
    },
    error: _ => {
      void router.navigate(['/login']);
      return false
    },
  });
};
