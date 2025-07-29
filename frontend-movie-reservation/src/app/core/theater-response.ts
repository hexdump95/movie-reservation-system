export interface TheaterResponse {
  id: number;
  number: number;
}

export interface CreateTheaterRequest {
  number: number;
  seatsGrid: boolean[][];
}
