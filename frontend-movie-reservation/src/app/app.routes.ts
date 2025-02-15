import {Routes} from '@angular/router';
import {authGuard} from "./auth/auth.guard";
import {LoginComponent} from "./auth/login/login.component";
import {HomeComponent} from "./pages/home/home.component";
import {MovieComponent} from "./movie/movie.component";

export const routes: Routes = [
  {path: 'login', component: LoginComponent},
  {path: '', component: HomeComponent, canActivate: [authGuard],},
  {path: 'movies', component: MovieComponent, canActivate: [authGuard],},
  {
    path: 'book',
    loadChildren: () => import(`./book/book.routes`),
    canActivate: [authGuard],
  },
];
