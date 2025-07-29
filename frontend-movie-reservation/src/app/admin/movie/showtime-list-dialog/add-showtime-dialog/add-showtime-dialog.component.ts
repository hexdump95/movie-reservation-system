import {Component, Inject} from '@angular/core';
import {
  MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle
} from "@angular/material/dialog";
import {FormBuilder, ReactiveFormsModule, Validators} from "@angular/forms";
import {TheaterService} from "../../../../core/theater.service";
import {MatFormField, MatLabel} from "@angular/material/form-field";
import {MatOption} from "@angular/material/core";
import {MatSelect} from "@angular/material/select";
import {MatInput} from "@angular/material/input";
import {AddShowtimeRequest, UnavailableDate} from "../../../../core/movie-response";
import {TheaterResponse} from "../../../../core/theater-response";
import {MatButton} from "@angular/material/button";
import {MovieService} from "../../../../core/movie.service";

@Component({
  selector: 'app-add-showtime-dialog',
  standalone: true,
  imports: [
    MatDialogActions,
    MatDialogContent,
    MatDialogTitle,
    ReactiveFormsModule,
    MatFormField,
    MatLabel,
    MatOption,
    MatSelect,
    MatInput,
    MatButton
  ],
  templateUrl: './add-showtime-dialog.component.html',
  styleUrl: './add-showtime-dialog.component.css'
})
export class AddShowtimeDialogComponent {
  showtimeForm!: any;
  theaters!: TheaterResponse[];
  unavailableDates!: UnavailableDate[];
  formSubmitted = false;

  constructor(
    public dialogRef: MatDialogRef<AddShowtimeDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public movieId: number,
    private fb: FormBuilder,
    private theaterService: TheaterService,
    private movieService: MovieService,
  ) {
  }

  ngOnInit() {
    this.theaterService.getTheaters().subscribe(theaters => {
      this.theaters = theaters;
    });
    this.showtimeForm = this.fb.group({
      theaterId: [null, [Validators.required]],
      dateStart: ['', Validators.required],
    });
  }

  onSubmit() {
    this.formSubmitted = true;
    // TODO: also check if date is available
    const showtime: AddShowtimeRequest = {
      theaterId: this.showtimeForm.value?.theaterId,
      dateStart: this.showtimeForm.value?.dateStart,
    }
    this.movieService.addShowtime(this.movieId, showtime)
      .subscribe(res => {
        this.formSubmitted = false;
        this.dialogRef.close(res);
      });

  }

  getUnavailableDates(theaterId: number) {
    this.theaterService.getUnavailableDates(theaterId)
      .subscribe(res => {
          this.unavailableDates = res;
        }
      )
  }

  onCancel() {
    this.dialogRef.close();
  }

}
