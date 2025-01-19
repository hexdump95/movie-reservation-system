import {HttpInterceptorFn} from '@angular/common/http';

export const authInterceptor: HttpInterceptorFn =
  (req, next) => {
    const authToken = localStorage.getItem("access_token");

    const authReq = authToken ? req.clone({
      setHeaders: {
        Authorization: `Bearer ${authToken}`,
      }
    }) : req;

    return next(authReq);
  };
