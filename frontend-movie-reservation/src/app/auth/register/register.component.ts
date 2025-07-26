import {Component} from '@angular/core';
import {MatFormField, MatFormFieldModule} from "@angular/material/form-field";
import {MatInput, MatInputModule} from "@angular/material/input";
import {FormBuilder, FormsModule, ReactiveFormsModule, Validators} from "@angular/forms";
import {MatButton, MatButtonModule, MatIconButton} from "@angular/material/button";
import {MatIcon, MatIconModule} from "@angular/material/icon";
import {Router} from "@angular/router";
import {MatDialogModule} from "@angular/material/dialog";
import {environment} from "../../../environments/environment";
import {AuthService} from "../auth.service";
import {LoginRequest} from "../interfaces/login-request";
import {passwordMatchValidator} from "../password-match-validator";

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [
    MatFormField,
    MatInput,
    FormsModule,
    MatButton,
    MatIcon,
    MatIconButton,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    ReactiveFormsModule,
    MatDialogModule,
  ],
  templateUrl: './register.component.html',
  styleUrl: './register.component.css'
})
export class RegisterComponent {
  pageTitle = environment.pageTitle;
  hide = true;

  formRegister = this.fb.group({
    username: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(8)]],
    password2: ['', Validators.required]
  }, {validators: passwordMatchValidator()});

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
  ) {
  }

  registerSubmitted = false;

  onSubmit() {
    if (!this.formRegister.valid)
      return;

    this.registerSubmitted = !this.registerSubmitted;
    const user: LoginRequest = {
      username: this.formRegister.value.username!,
      password: this.formRegister.value.password!,
    };

    this.authService.register(user).subscribe(
      {
        next: res => {
          let authHeader = res.access_token;
          if (authHeader.length > 0) {
            this.authService.setToken(authHeader);
          }
          void this.router.navigate(["/"]);
        },
        error: _ => {
          this.registerSubmitted = !this.registerSubmitted;
        }
      }
    );
  }

}
