import {Component} from '@angular/core';
import {MatFormField, MatLabel} from "@angular/material/form-field";
import {FormBuilder, FormsModule, ReactiveFormsModule, Validators} from "@angular/forms";
import {MatButton} from "@angular/material/button";
import {MatInput} from "@angular/material/input";
import {BookService} from "../book.service";
import {ActivatedRoute} from "@angular/router";

@Component({
  selector: 'app-pay',
  standalone: true,
  imports: [
    MatLabel,
    FormsModule,
    MatButton,
    MatFormField,
    MatInput,
    ReactiveFormsModule
  ],
  templateUrl: './pay.component.html',
  styleUrl: './pay.component.css'
})
export class PayComponent {
  formPay = this.fb.group({
    cardNumber: ['', [Validators.required, Validators.pattern('[0-9]*')]],
  });
  paySubmitted = false;
  showtimeId!: number;

  constructor(
    private fb: FormBuilder,
    private bookService: BookService,
    private route: ActivatedRoute,
  ) {
    this.showtimeId = this.route.snapshot.params['id'];
  }

  onSubmit() {
    if (!this.formPay.valid)
      return;

    this.paySubmitted = !this.paySubmitted;
    this.bookService
      .paySeats(this.showtimeId)
      .subscribe(res => {
        console.log(res);
      });
  }
}
