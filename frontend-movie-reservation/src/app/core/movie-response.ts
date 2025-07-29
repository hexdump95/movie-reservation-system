export interface GetMovieResponse {
  id: number;
  title: string;
  year: number;
  genreName: string;
}

export interface GetMovieDetailResponse {
  title: string;
  description: string;
  posterImage: string;
  duration: number;
  releaseDate: string;
  year: number;
  genre: GetMovieDetailGenreResponse;
}

export interface GetMovieDetailGenreResponse {
  id: number;
  name: string;
}

export interface CreateMovieRequest {
  title: string;
  description: string;
  posterImage: string;
  duration: number;
  releaseDate: string;
  year: number;
  genreId: number;
}

export interface UpdateMovieRequest {
  title: string;
  description: string;
  posterImage: string;
  duration: number;
  releaseDate: string;
  year: number;
  genreId: number;
}

export interface UpdateMovieResponse {
  id: number;
  title: string;
  year: number;
  genreName: string;
}

export interface CreateMovieResponse {
  id: number;
  title: string;
  year: number;
  genreName: string;
}

export interface GetShowtimeResponse {
  id: number;
  dateStart: string;
  dateEnd: string;
  theaterId: number;
  theaterNumber: number;
  hasBooks: boolean;
}

export interface UnavailableDate {
  from: Date;
  to: Date;
}

export interface AddShowtimeRequest {
  dateStart: string;
  theaterId: number;
}

export interface AddShowtimeResponse {
  id: number;
  dateStart: string;
  dateEnd: string;
  theaterId: number;
  theaterNumber: number;
  hasBooks: boolean;
}
