export interface BookDetailResponse {
  movieTitle: string;
  bookStatus: string;
  bookCreatedAt: Date;
  showtimeDateStart: Date;
  theaterNumber: number;
  seats: BookSeatDetailResponse[];
  bookTotalPrice: number;
}

export interface BookSeatDetailResponse {
  seatCode: string;
  price: number;
}
