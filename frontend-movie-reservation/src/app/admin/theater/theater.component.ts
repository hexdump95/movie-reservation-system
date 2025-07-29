import {Component} from '@angular/core';
import {MatButton} from "@angular/material/button";
import {RouterLink} from "@angular/router";
import {TheaterService} from "../../core/theater.service";
import {TheaterResponse} from "../../core/theater-response";

@Component({
  selector: 'app-theater',
  standalone: true,
  imports: [
    MatButton,
    RouterLink
  ],
  templateUrl: './theater.component.html',
  styleUrl: './theater.component.css'
})
export class TheaterComponent {
  theaters!: TheaterResponse[];

  constructor(private theaterService: TheaterService) {
  }

  ngOnInit() {
    this.theaterService.getTheaters().subscribe(res => {
        this.theaters = res;
      }
    );
  }

  createTheater() {

  }
}
