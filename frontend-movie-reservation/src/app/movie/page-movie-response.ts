export interface PageMovieResponse {

  totalPages: number;
  currentPage: number;
  pageSize: number;
  hasPreviousPage: boolean;
  hasNextPage: boolean;
  data: MovieResponse[];
}

export interface MovieResponse {
  id: number;
  title: string;
  posterImage: string;
  hasShowtime: boolean;
}
