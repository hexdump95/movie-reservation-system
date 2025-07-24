export interface PageBookResponse {
  totalPages: number;
  currentPage: number;
  pageSize: number;
  hasPreviousPage: boolean;
  hasNextPage: boolean;
  data: BookResponse[];
}

export interface BookResponse {
  bookId: number;
  bookTotalPrice: number;
  bookStatus: string;
  bookCreatedAt: Date;
  showtimeDateStart: Date;
  movieTitle: string;
  theaterNumber: number;
  totalSeats: number;
}
