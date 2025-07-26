import {Routes} from '@angular/router';
import {authGuard} from "./auth/auth.guard";
import {LoginComponent} from "./auth/login/login.component";
import {HomeComponent} from "./pages/home/home.component";
import {MovieComponent} from "./movie/movie.component";
import {ReportComponent} from "./report/report.component";

export const routes: Routes = [
  {path: 'login', component: LoginComponent},
  {path: '', component: HomeComponent, canActivate: [authGuard],},
  {path: 'movies', component: MovieComponent, canActivate: [authGuard],},
  {
    path: 'book',
    loadChildren: () => import(`./book/book.routes`),
    canActivate: [authGuard],
  },
  {
    path: 'admin',
    loadChildren: () => import(`./admin/admin.routes`),
    canActivate: [authGuard],
    data: {roles: ['ROLE_ADMIN']}
  },
  {
    path: 'reports',
    component: ReportComponent,
    canActivate: [authGuard],
    data: {permissions: ['read:reports']}
  },
];
