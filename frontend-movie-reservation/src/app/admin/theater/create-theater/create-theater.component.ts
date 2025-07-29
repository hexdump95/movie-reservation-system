import {Component} from '@angular/core';
import {FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators} from "@angular/forms";
import {NgStyle} from "@angular/common";
import {MatButton} from "@angular/material/button";
import {MatDialogContent} from "@angular/material/dialog";
import {MatFormField, MatLabel} from "@angular/material/form-field";
import {MatInput} from "@angular/material/input";
import {CreateTheaterRequest} from "../../../core/theater-response";
import {TheaterService} from "../../../core/theater.service";
import {Router} from "@angular/router";

@Component({
  selector: 'app-create-theater',
  standalone: true,
  imports: [
    FormsModule,
    NgStyle,
    MatButton,
    ReactiveFormsModule,
    MatDialogContent,
    MatFormField,
    MatInput,
    MatLabel
  ],
  templateUrl: './create-theater.component.html',
  styleUrl: './create-theater.component.css'
})
export class CreateTheaterComponent {
  grid!: boolean[][];
  sizeX: number = 5;
  sizeY: number = 5;
  seatLength: number = 0;
  formTheater!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private theaterService: TheaterService,
    private router: Router,
  ) {
  }

  ngOnInit() {
    this.formTheater = this.fb.group({
      number: [null, Validators.required],
    });
    this.updateGrid();
  }

  updateGrid(event?: Event) {
    this.sizeY = this.sizeY > 26 ? 26 : this.sizeY < 5 ? 5 : this.sizeY;
    this.sizeX = this.sizeX > 26 ? 26 : this.sizeX < 5 ? 5 : this.sizeX;
    this.grid = Array.from({length: this.sizeY}, () => Array(this.sizeX).fill(true));
    this.seatLength = (window.innerWidth - 160) / this.sizeX;
  }

  selectSeat(row: number, column: number) {
    this.grid[row][column] = !this.grid[row][column];
  }

  onSubmit() {
    console.log(this.grid);
    const createTheaterRequest: CreateTheaterRequest = {
      number: this.formTheater.value?.number,
      seatsGrid: this.grid,
    };
    this.theaterService.createTheater(createTheaterRequest)
      .subscribe(_ => {
        void this.router.navigate(['../']);
      });
  }
}
