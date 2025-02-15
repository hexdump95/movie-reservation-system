export interface ShowtimeResponse {
  movieTitle: string;
  theaterNumber: string;
  dateStart: string;
  seats: SeatResponse[][];
}

export interface SeatResponse {
  id: number;
  column: number;
  row: number;
  code: string;
  isOccupied: boolean;
  isSelected: boolean;
}
