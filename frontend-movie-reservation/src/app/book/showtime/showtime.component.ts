import {Component} from '@angular/core';
import {BookService} from "../book.service";
import {ActivatedRoute, Router} from "@angular/router";
import {DatePipe, NgStyle} from "@angular/common";
import {LoaderComponent} from "../../shared/loader/loader.component";
import {MatButton} from "@angular/material/button";
import {ShowtimeResponse} from "./showtime-response";
import {WebSocketService} from '../../shared/websocket.service';
import {interval, Subscription} from "rxjs";
import {AuthService} from "../../auth/auth.service";
import {SeatUpdate} from "../../shared/seat-update.interface";

@Component({
  selector: 'app-showtime',
  standalone: true,
  imports: [
    NgStyle,
    LoaderComponent,
    DatePipe,
    MatButton,
  ],
  templateUrl: './showtime.component.html',
  styleUrl: './showtime.component.css'
})
export class ShowtimeComponent {
  showtime!: ShowtimeResponse;

  showtimeId!: number;

  maxSelectableSeats = 10;
  selectedSeats = 0;
  currentDate = new Date();
  seatLength = 0;
  seatUpdateSubscription!: Subscription;
  seatRefreshSubscription!: Subscription;

  constructor(private bookService: BookService,
              private route: ActivatedRoute,
              private websocketService: WebSocketService,
              private authService: AuthService,
              private router: Router,
  ) {
  }

  ngOnInit() {
    this.showtimeId = this.route.snapshot.params['id'];
    this.refreshSeats();
    this.bookService.getCentrifugoToken(this.showtimeId)
      .subscribe(res => {
        this.websocketService.connect(res.ws_token, res.channel)
      });
    this.subscribeToSeatUpdates();

    this.seatRefreshSubscription = interval(20000).subscribe(() => {
      this.refreshSeats();
    });

  }

  refreshSeats(): void {
    this.bookService.getSeatsByShowtimeId(this.showtimeId)
      .subscribe(res => {
        this.showtime = res;
        this.selectedSeats = 0;
        this.seatLength = (window.innerWidth - 160) / this.showtime.seats[0].slice(-1)[0].column;
        this.showtime.seats.flatMap(x => x)
          .forEach(x => {
            if (x.isSelected) {
              this.selectedSeats++;
            }
          });
        this.maxSelectableSeats = Math.min(this.maxSelectableSeats, this.showtime.seats.flatMap(x => x).filter(x => !x.isOccupied && x.code != '').length);
      });
  }

  ngOnDestroy(): void {
    this.websocketService.disconnect();
    this.seatUpdateSubscription?.unsubscribe();
    this.seatRefreshSubscription?.unsubscribe();
  }

  subscribeToSeatUpdates(): void {
    this.seatUpdateSubscription = this.websocketService.seatUpdates$.subscribe({
      next: (update: SeatUpdate | null) => {

        if (update && update.userEmail !== this.authService.getUserPayload().sub) {
          const seat = this.showtime.seats
            .flatMap(x => x)
            .find(x => x.id === update.seatId)!;
          seat.isOccupied = update.isReserved;
          if (update.isReserved)
            this.selectedSeats--;
        }
      }
    });
  }

  selectSeat(row: number, column: number) {
    const seat = this.showtime.seats[row - 1][column - 1];
    if (seat.code !== '' && !seat.isOccupied) {
      if (this.selectedSeats >= this.maxSelectableSeats) {
        if (seat.isSelected) {
          seat.isSelected = false;
          this.selectedSeats--;
        }
      } else {
        this.bookService.temporaryBookSeat(this.showtimeId, seat.id)
          .subscribe(
            {
              next: _ => {
                seat.isSelected = !seat.isSelected;
                this.selectedSeats += seat.isSelected ? 1 : -1;
              }, error: _ => {
                seat.isOccupied = true;
              }
            }
          );
      }
    }
  }

  payTickets() {
    this.bookService.holdSeats(this.showtimeId)
      .subscribe(_ => {
        void this.router.navigate([`/book/showtimes/${this.showtimeId}/pay`]);
      })
  }

}
