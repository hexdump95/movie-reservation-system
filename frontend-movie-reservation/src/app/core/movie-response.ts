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
