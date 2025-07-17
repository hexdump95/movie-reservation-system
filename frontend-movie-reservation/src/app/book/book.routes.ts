import {Routes} from '@angular/router';
import {BookComponent} from "./book.component";
import {ShowtimeComponent} from "./showtime/showtime.component";
import {PayComponent} from "./pay/pay.component";

const bookRoutes: Routes = [
  {
    path: '',
    component: BookComponent,
  },
  {
    path: 'showtimes/:id/pay',
    component: PayComponent,
  },
  {
    path: 'showtimes/:id',
    component: ShowtimeComponent,
  },
];

export default bookRoutes;
