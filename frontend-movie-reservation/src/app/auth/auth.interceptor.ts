import {HttpInterceptorFn} from '@angular/common/http';
import {AuthService} from "./auth.service";
import {inject} from "@angular/core";

export const authInterceptor: HttpInterceptorFn =
  (req, next) => {
    const authService: AuthService = inject(AuthService);
    const authToken = authService.getToken();

    const authReq = authToken ? req.clone({
      setHeaders: {
        Authorization: `Bearer ${authToken}`,
      }
    }) : req;

    return next(authReq);
  };
