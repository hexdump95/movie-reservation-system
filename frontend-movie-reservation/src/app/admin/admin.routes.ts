import {Routes} from '@angular/router';
import {AdminComponent} from "./admin.component";
import {UserComponent} from "./user/user.component";
import {MovieComponent} from "./movie/movie.component";
import {ReportComponent} from "./report/report.component";

const adminRoutes: Routes = [
  {
    path: '',
    component: AdminComponent,
  },
  {
    path: 'users',
    component: UserComponent,
  },
  {
    path: 'movies',
    component: MovieComponent,
  },
  {
    path: 'reports',
    component: ReportComponent,
  },
];

export default adminRoutes;
