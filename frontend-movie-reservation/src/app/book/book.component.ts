import {Component} from '@angular/core';
import {BookService} from "./book.service";
import {RouterLink} from "@angular/router";
import {CurrencyPipe, DatePipe, NgStyle, TitleCasePipe} from "@angular/common";
import {PageBookResponse} from "./book-response";
import {MatButton} from "@angular/material/button";
import {MatDialog} from "@angular/material/dialog";
import {ConfirmDialogComponent} from "../shared/confirm-dialog/confirm-dialog.component";

@Component({
  selector: 'app-book',
  standalone: true,
  imports: [
    RouterLink,
    DatePipe,
    CurrencyPipe,
    TitleCasePipe,
    NgStyle,
    MatButton,
  ],
  templateUrl: './book.component.html',
  styleUrl: './book.component.css'
})
export class BookComponent {
  page!: PageBookResponse;
  loadingPage: boolean = false;
  currentPage: number = 1;
  minPageNumber = 1;
  maxPageNumber = 10;
  pageRange: number[] = [];

  constructor(private bookService: BookService, private dialog: MatDialog) {
  }

  ngOnInit() {
    this.bookService.getReservations(this.currentPage).subscribe(
      (res: PageBookResponse) => {
        this.page = res;
        this.maxPageNumber = res.totalPages >= 10 ? 10 : res.totalPages;
        this.updatePageRange();
      }
    );
  }

  updatePageRange(): void {
    if (this.currentPage >= 6) {
      if (this.currentPage + 4 > this.page.totalPages) {
        this.minPageNumber = this.page.totalPages - 9;
        this.maxPageNumber = this.page.totalPages;
      } else {
        this.minPageNumber = this.currentPage - 5;
        this.maxPageNumber = this.currentPage + 4;
      }
    } else {
      this.minPageNumber = 1;
      this.maxPageNumber = this.page.totalPages <= 10 ? this.page.totalPages : 10;
    }
    let range = [];
    for (let i = this.minPageNumber; i <= this.maxPageNumber; i++) {
      range.push(i);
    }
    this.pageRange = range;
  }

  previousPage() {
    this.bookService.getReservations(--this.currentPage)
      .subscribe((response: PageBookResponse) => {
        this.page = response;
        this.loadingPage = false;
        this.updatePageRange();
      });
  }

  nextPage() {
    this.bookService.getReservations(++this.currentPage)
      .subscribe((response: PageBookResponse) => {
        this.page = response;
        this.loadingPage = false;
        this.updatePageRange();
      });
  }

  goToPage(page: number) {
    this.bookService.getReservations(page)
      .subscribe((response: PageBookResponse) => {
        this.page = response;
        this.currentPage = page;
        this.loadingPage = false;
        this.updatePageRange();
      });
  }

  openCancelDialog(bookId: number): void {
    const buttonElement = document.activeElement as HTMLElement;
    buttonElement.blur();
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      data: {
        title: 'Cancel Reservation',
        message: 'Are you sure you want to cancel the reservation?'
      },
      disableClose: true
    });

    dialogRef.afterClosed().subscribe(res => {
        if (res) {
          this.bookService.cancelReservation(bookId)
            .subscribe(res => {
              if (res.success) {
                this.page.data = this.page.data.map(x => {
                  if (x.bookId === bookId)
                    x.bookStatus = 'CANCELED';
                  return x;
                });
              }
            });
        }
      }
    );
  }
}
