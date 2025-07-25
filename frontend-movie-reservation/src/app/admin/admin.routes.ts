import {Routes} from '@angular/router';
import {AdminComponent} from "./admin.component";
import {UserComponent} from "./user/user.component";

const adminRoutes: Routes = [
  {
    path: '',
    component: AdminComponent,
  },
  {
    path: 'users',
    component: UserComponent,
  }
];

export default adminRoutes;
