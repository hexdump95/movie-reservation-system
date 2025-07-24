import {Component} from '@angular/core';
import {ActivatedRoute} from "@angular/router";
import {BookService} from "../book.service";
import {LoaderComponent} from "../../shared/loader/loader.component";
import {BookDetailResponse} from "./book-detail-response";
import {CurrencyPipe, DatePipe, TitleCasePipe} from "@angular/common";

@Component({
  selector: 'app-detail',
  standalone: true,
  imports: [
    LoaderComponent,
    CurrencyPipe,
    DatePipe,
    TitleCasePipe
  ],
  templateUrl: './book-detail.component.html',
  styleUrl: './book-detail.component.css'
})
export class BookDetailComponent {
  bookId!: number;
  book!: BookDetailResponse;

  constructor(private route: ActivatedRoute, private bookService: BookService) {
  }

  ngOnInit() {
    this.bookId = this.route.snapshot.params['id'];
    this.bookService.getOneReservation(this.bookId).subscribe(
      (res: BookDetailResponse) => {
        this.book = res;
      }
    )
  }

}
