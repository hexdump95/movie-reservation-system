import {Component} from '@angular/core';
import {BookService} from "../book.service";
import {ActivatedRoute} from "@angular/router";
import {DatePipe, NgStyle} from "@angular/common";
import {LoaderComponent} from "../../shared/loader/loader.component";
import {MatButton} from "@angular/material/button";
import {ShowtimeResponse} from "./showtime-response";

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

  constructor(private bookService: BookService, private route: ActivatedRoute) {
  }

  ngOnInit() {
    this.showtimeId = this.route.snapshot.params['id'];
    this.bookService.getSeatsByShowtimeId(this.showtimeId)
      .subscribe(res => {
        this.showtime = res;
        this.seatLength = (window.innerWidth - 160) / this.showtime.seats[0].slice(-1)[0].column;

        this.maxSelectableSeats = Math.min(this.maxSelectableSeats, this.showtime.seats.flatMap(x => x).filter(x => !x.isOccupied && x.code != '').length);
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
        seat.isSelected = !seat.isSelected;
        this.selectedSeats += seat.isSelected ? 1 : -1;
      }
    }
  }

  payTickets() {
    let ids = this.showtime.seats
      .flatMap(x => x)
      .filter(x => x.isSelected)
      .map(x => ({id: x.id}));
    console.log(ids);
  }

}
